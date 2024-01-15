<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\Video;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Delete\DeleteVideoUseCase;
use Core\UseCase\Video\Delete\DTO\VideoDeleteInputDto;
use Tests\TestCase;


class DeleteVideoUseCaseTest extends TestCase
{

    public function testDelete(): void
    {
        $video = Video::factory()->create();

        $useCase = new DeleteVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );

        $response = $useCase->execute(new VideoDeleteInputDto(
            id: $video->id
        ));

        $this->assertTrue($response->success);
    }

    public function testDeleteIdNotFound()
    {
        $this->expectException(NotFoundException::class);

        $useCase = new DeleteVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );
        $useCase->execute(new VideoDeleteInputDto(
            id: 'fake_id'
        ));
    }

}
