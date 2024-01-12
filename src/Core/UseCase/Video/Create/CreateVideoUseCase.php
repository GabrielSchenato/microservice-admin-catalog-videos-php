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
use Core\Domain\ValueObject\Media;
use Core\UseCase\Interfaces\FileStorageInterface;
use Core\UseCase\Interfaces\TransactionDbInterface;
use Core\UseCase\Video\Create\DTO\VideoCreateInputDto;
use Core\UseCase\Video\Create\DTO\VideoCreateOutputDto;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Throwable;

class CreateVideoUseCase
{
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
        $video = $this->createEntity($input);

        try {
            $newVideo = $this->repository->insert($video);

            if ($pathMedia = $this->storeMedia($newVideo->id, $input->videoFile)) {
                $media = new Media(
                    filePath: $pathMedia,
                    mediaStatus: MediaStatus::PROCESSING
                );
                $video->setVideoFile($media);
                $this->repository->updateMedia($video);

                $this->eventManager->dispatch(new VideoCreatedEvent($newVideo));
            }

            $videoCreateOutputDto = $this->output($video);

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
        $video = new VideoEntity(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating
        );

        $this->validateCategoriesId($input->categoriesId);
        foreach ($input->categoriesId as $categoryId) {
            $video->addCategory($categoryId);
        }

        $this->validateGenresId($input->genresId);
        foreach ($input->genresId as $genreId) {
            $video->addGenre($genreId);
        }

        $this->validateCastMembersId($input->castMembersId);
        foreach ($input->castMembersId as $castMemberId) {
            $video->addCastMember($castMemberId);
        }

        return $video;
    }

    private function storeMedia(string $path, ?array $media = null): string
    {
        if ($media) {
            return
                $this->storage->store(
                    path: $path,
                    file: $media
                );
        }

        return '';
    }

    /**
     * @throws NotFoundException
     */
    private function validateCategoriesId(array $categoriesId = []): void
    {
        $categoriesDb = $this->categoryRepository->getIdsListIds($categoriesId);

        $arrayDiff = array_diff($categoriesId, $categoriesDb);
        $count = count($arrayDiff);
        if ($count > 0) {
            $msg = sprintf(
                '%s %s not found',
                $count > 1 ? 'Categories' : 'Category',
                implode(',', $arrayDiff)
            );
            throw new NotFoundException($msg);
        }
    }

    /**
     * @throws NotFoundException
     */
    private function validateGenresId(array $genresId = []): void
    {
        $genresDb = $this->genreRepository->getIdsListIds($genresId);

        $arrayDiff = array_diff($genresId, $genresDb);
        $count = count($arrayDiff);
        if ($count > 0) {
            $msg = sprintf(
                '%s %s not found',
                $count > 1 ? 'Genres' : 'Genre',
                implode(',', $arrayDiff)
            );
            throw new NotFoundException($msg);
        }
    }

    /**
     * @throws NotFoundException
     */
    private function validateCastMembersId(array $castMembersId = []): void
    {
        $castMembersDb = $this->castMemberRepository->getIdsListIds($castMembersId);

        $arrayDiff = array_diff($castMembersId, $castMembersDb);
        $count = count($arrayDiff);
        if ($count > 0) {
            $msg = sprintf(
                '%s %s not found',
                $count > 1 ? 'Cast Members' : 'Cast Member',
                implode(',', $arrayDiff)
            );
            throw new NotFoundException($msg);
        }
    }

    private function output(VideoEntity $entity): VideoCreateOutputDto
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
