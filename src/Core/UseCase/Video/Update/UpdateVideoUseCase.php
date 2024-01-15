<?php

namespace Core\UseCase\Video\Update;

use Core\Domain\Builder\Video\BuilderInterface;
use Core\Domain\Builder\Video\UpdateVideoBuilder;
use Core\UseCase\Video\BaseVideoUseCase;
use Core\UseCase\Video\Update\DTO\VideoUpdateInputDto;
use Core\UseCase\Video\Update\DTO\VideoUpdateOutputDto;
use Throwable;

class UpdateVideoUseCase extends BaseVideoUseCase
{
    public function execute(VideoUpdateInputDto $input): VideoUpdateOutputDto
    {
        $this->validateAllIds($input);

        $entity = $this->repository->findById($input->id);

        $entity->update(
            title: $input->title,
            description: $input->description
        );

        $this->builder->setEntity($entity);

        try {
            $this->repository->update($this->builder->getEntity());

            $this->storageFiles($input);
            $this->repository->updateMedia($this->builder->getEntity());

            $videoUpdateOutputDto = $this->output();

            $this->transaction->commit();

            return $videoUpdateOutputDto;
        } catch (Throwable $th) {
            $this->transaction->rollback();

            if (isset($pathMedia)) $this->storage->delete($pathMedia);

            throw $th;
        }
    }

    private function output(): VideoUpdateOutputDto
    {
        $entity = $this->builder->getEntity();

        return new VideoUpdateOutputDto(
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

    protected function getBuilder(): BuilderInterface
    {
        return new UpdateVideoBuilder();
    }
}
