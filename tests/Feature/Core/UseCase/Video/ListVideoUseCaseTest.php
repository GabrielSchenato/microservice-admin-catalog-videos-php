<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\Video;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\List\DTO\VideoListInputDto;
use Core\UseCase\Video\List\ListVideoUseCase;
use Tests\TestCase;

class ListVideoUseCaseTest extends TestCase
{
    public function testList(): void
    {
        $video = Video::factory()->create();

        $useCase = new ListVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );

        $response = $useCase->execute(new VideoListInputDto(
            id: $video->id
        ));

        $this->assertNotNull($response);
        $this->assertEquals($video->id, $response->id);
    }

    public function testListIdNotFound()
    {
        $this->expectException(NotFoundException::class);

        $useCase = new ListVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );
        $useCase->execute(new VideoListInputDto(
            id: 'fake_id'
        ));
    }
}
