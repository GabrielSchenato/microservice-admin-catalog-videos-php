<?php

namespace Core\UseCase\Video\Delete;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Delete\DTO\VideoDeleteInputDto;
use Core\UseCase\Video\Delete\DTO\VideoDeleteOutputDto;

class DeleteVideoUseCase
{
    public function __construct(
        protected VideoRepositoryInterface $repository
    )
    {
    }

    public function execute(VideoDeleteInputDto $input): VideoDeleteOutputDto
    {
        $success = $this->repository->delete($input->id);

        return new VideoDeleteOutputDto(
            success: $success
        );
    }
}
