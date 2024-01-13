<?php

namespace Core\UseCase\Video\Create;

use Core\Domain\Entity\VideoEntity;
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
use Core\UseCase\Video\Create\DTO\VideoCreateInputDto;
use Core\UseCase\Video\Create\DTO\VideoCreateOutputDto;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Throwable;

class CreateVideoUseCase
{
    protected VideoEntity $entity;

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
    }


    public function execute(VideoCreateInputDto $input): VideoCreateOutputDto
    {
        $this->validateAllIds($input);
        $this->entity = $this->createEntity($input);

        try {
            $this->repository->insert($this->entity);

            $this->storageFiles($input);
            $this->repository->updateMedia($this->entity);

            $videoCreateOutputDto = $this->output($this->entity);

            $this->transaction->commit();

            return $videoCreateOutputDto;
        } catch (Throwable $th) {
            $this->transaction->rollback();

            if (isset($pathMedia)) $this->storage->delete($pathMedia);

            throw $th;
        }
    }

    private function createEntity(VideoCreateInputDto $input): VideoEntity
    {
        $this->entity = new VideoEntity(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating
        );

        foreach ($input->categoriesId as $categoryId) {
            $this->entity->addCategory($categoryId);
        }
        foreach ($input->genresId as $genreId) {
            $this->entity->addGenre($genreId);
        }
        foreach ($input->castMembersId as $castMemberId) {
            $this->entity->addCastMember($castMemberId);
        }

        return $this->entity;
    }

    private function storageFiles(object $input): void
    {
        if ($pathVideoFile = $this->storeFile($this->entity->id, $input->videoFile)) {
            $media = new Media(
                filePath: $pathVideoFile,
                mediaStatus: MediaStatus::PROCESSING
            );
            $this->entity->setVideoFile($media);
            $this->eventManager->dispatch(new VideoCreatedEvent($this->entity));
        }

        if ($pathTrailerFile = $this->storeFile($this->entity->id, $input->trailerFile)) {
            $media = new Media(
                filePath: $pathTrailerFile,
                mediaStatus: MediaStatus::PROCESSING
            );
            $this->entity->setTrailerFile($media);
        }

        if ($pathBannerFile = $this->storeFile($this->entity->id, $input->bannerFile)) {
            $this->entity->setBannerFile(new Image(
                path: $pathBannerFile
            ));
        }

        if ($pathThumbFile = $this->storeFile($this->entity->id, $input->thumbFile)) {
            $this->entity->setThumbFile(new Image(
                path: $pathThumbFile
            ));
        }

        if ($pathThumbHalf = $this->storeFile($this->entity->id, $input->thumbHalf)) {
            $this->entity->setThumbHalf(new Image(
                path: $pathThumbHalf
            ));
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

    private
    function output(VideoEntity $entity): VideoCreateOutputDto
    {
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
