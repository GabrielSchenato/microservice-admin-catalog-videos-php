<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\Video;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\ChangeEncoded\ChangeEncodedPathVideoUseCase;
use Core\UseCase\Video\ChangeEncoded\DTO\VideoChangeEncodedInputDto;
use Tests\TestCase;


class ChangeEncodedPathVideoUseCaseTest extends TestCase
{

    public function testIfUpdatedMediaInDatabase()
    {
        $video = Video::factory()->create();

        $useCase = new ChangeEncodedPathVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );

        $input = new VideoChangeEncodedInputDto(
            id: $video->id,
            encodedPath: 'path-id/video_encoded.ext',
        );

        $useCase->execute($input);

        $this->assertDatabaseHas('media_videos', [
            'video_id' => $input->id,
            'encoded_path' => $input->encodedPath,
        ]);
    }

}
