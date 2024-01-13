<?php

namespace Core\UseCase\Video\List;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\List\DTO\VideoListInputDto;
use Core\UseCase\Video\List\DTO\VideoListOutputDto;

class ListVideoUseCase
{

    public function __construct(private VideoRepositoryInterface $repository)
    {
    }

    public function execute(VideoListInputDto $input): VideoListOutputDto
    {
        $entity = $this->repository->findById($input->id);

        return new VideoListOutputDto(
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
