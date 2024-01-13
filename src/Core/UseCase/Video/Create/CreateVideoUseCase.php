<?php

namespace Core\UseCase\Video\Create;

use Core\Domain\Enum\MediaStatus;
use Core\Domain\Events\VideoCreatedEvent;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;
use Core\UseCase\Interfaces\FileStorageInterface;
use Core\UseCase\Interfaces\TransactionDbInterface;
use Core\UseCase\Video\Builder\BuilderVideo;
use Core\UseCase\Video\Create\DTO\VideoCreateInputDto;
use Core\UseCase\Video\Create\DTO\VideoCreateOutputDto;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Throwable;

class CreateVideoUseCase
{
    protected BuilderVideo $builder;

    public function __construct(
        protected VideoRepositoryInterface      $repository,
        protected TransactionDbInterface        $transaction,
        protected FileStorageInterface          $storage,
        protected VideoEventManagerInterface    $eventManager,
        protected CategoryRepositoryInterface   $categoryRepository,
        protected GenreRepositoryInterface      $genreRepository,
        protected CastMemberRepositoryInterface $castMemberRepository,
    )
    {
        $this->builder = new BuilderVideo();
    }


    public function execute(VideoCreateInputDto $input): VideoCreateOutputDto
    {
        $this->validateAllIds($input);
        $this->builder->createEntity($input);

        try {
            $this->repository->insert($this->builder->getEntity());

            $this->storageFiles($input);
            $this->repository->updateMedia($this->builder->getEntity());

            $videoCreateOutputDto = $this->output();

            $this->transaction->commit();

            return $videoCreateOutputDto;
        } catch (Throwable $th) {
            $this->transaction->rollback();

            if (isset($pathMedia)) $this->storage->delete($pathMedia);

            throw $th;
        }
    }

    private function storageFiles(object $input): void
    {
        $path = $this->builder->getEntity()->id;

        if ($pathVideoFile = $this->storeFile($path, $input->videoFile)) {
            $this->builder->addMediaVideo($pathVideoFile, MediaStatus::PROCESSING);
            $this->eventManager->dispatch(new VideoCreatedEvent($this->builder->getEntity()));
        }

        if ($pathTrailerFile = $this->storeFile($path, $input->trailerFile)) {
            $this->builder->addTrailer($pathTrailerFile);
        }

        if ($pathBannerFile = $this->storeFile($path, $input->bannerFile)) {
            $this->builder->addBanner($pathBannerFile);
        }

        if ($pathThumbFile = $this->storeFile($path, $input->thumbFile)) {
            $this->builder->addThumb($pathThumbFile);
        }

        if ($pathThumbHalf = $this->storeFile($path, $input->thumbHalf)) {
            $this->builder->addThumbHalf($pathThumbHalf);
        }
    }

    private function storeFile(string $path, ?array $media = null): ?string
    {
        if ($media) {
            return
                $this->storage->store(
                    path: $path,
                    file: $media
                );
        }

        return null;
    }

    protected function validateAllIds(object $input)
    {
        $this->validateIds(
            repository: $this->categoryRepository,
            singularLabel: 'Category',
            ids: $input->categoriesId,
            pluralLabel: 'Categories'
        );

        $this->validateIds(
            repository: $this->genreRepository,
            singularLabel: 'Genre',
            ids: $input->genresId
        );

        $this->validateIds(
            repository: $this->castMemberRepository,
            singularLabel: 'Cast Member',
            ids: $input->castMembersId
        );
    }

    /**
     * @throws NotFoundException
     */
    protected function validateIds($repository, string $singularLabel, array $ids = [], ?string $pluralLabel = null): void
    {
        $idsDb = $repository->getIdsListIds($ids);

        $arrayDiff = array_diff($ids, $idsDb);
        $count = count($arrayDiff);
        if ($count > 0) {
            $msg = sprintf(
                '%s %s not found',
                $count > 1 ? $singularLabel . 's' : $singularLabel,
                implode(',', $arrayDiff)
            );
            throw new NotFoundException($msg);
        }
    }

    private function output(): VideoCreateOutputDto
    {
        $entity = $this->builder->getEntity();

        return new VideoCreateOutputDto(
            id: $entity->id(),
            title: $entity->title,
            description: $entity->description,
            yearLaunched: $entity->yearLaunched,
            duration: $entity->duration,
            opened: $entity->opened,
            rating: $entity->rating,
            categoriesId: $entity->categoriesId,
            genresId: $entity->genresId,
            castMembersId: $entity->castMembersId,
            thumbFile: $entity->getThumbFile()?->getPath(),
            thumbHalf: $entity->getThumbHalf()?->getPath(),
            bannerFile: $entity->getBannerFile()?->getPath(),
            trailerFile: $entity->getTrailerFile()?->filePath,
            videoFile: $entity->getVideoFile()?->filePath,
        );
    }
}
