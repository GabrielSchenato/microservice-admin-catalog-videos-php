<?php

namespace Core\UseCase\Video\ChangeEncoded;

use Core\Domain\Enum\MediaStatus;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Media;
use Core\UseCase\Video\ChangeEncoded\DTO\VideoChangeEncodedInputDto;
use Core\UseCase\Video\ChangeEncoded\DTO\VideoChangeEncodedOutputDto;

class ChangeEncodedPathVideoUseCase
{
    public function __construct(private readonly VideoRepositoryInterface $repository)
    {
    }

    public function execute(VideoChangeEncodedInputDto $input): VideoChangeEncodedOutputDto
    {
        $entity = $this->repository->findById($input->id);

        $entity->setVideoFile(
            new Media(
                filePath: $entity->getVideoFile()?->filePath ?? '',
                mediaStatus: MediaStatus::COMPLETE,
                encodedPath: $input->encodedPath
            )
        );

        $this->repository->updateMedia($entity);

        return new VideoChangeEncodedOutputDto(
            id: $entity->id(),
            encodedPath: $input->encodedPath
        );
    }

}
