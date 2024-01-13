<?php

namespace Core\UseCase\Video\Create;

use Core\UseCase\Video\BaseVideoUseCase;
use Core\UseCase\Video\Create\DTO\VideoCreateInputDto;
use Core\UseCase\Video\Create\DTO\VideoCreateOutputDto;
use Throwable;

class CreateVideoUseCase extends BaseVideoUseCase
{
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
