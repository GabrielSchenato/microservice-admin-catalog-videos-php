<?php

namespace Tests\Feature\Core\UseCase\Video;

use Core\Domain\Enum\Rating;
use Core\UseCase\Video\Create\CreateVideoUseCase;
use Core\UseCase\Video\Create\DTO\VideoCreateInputDto;
use Exception;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Support\Facades\Event;
use Tests\Stubs\UploadFilesStub;
use Tests\Stubs\VideoEventStub;
use Throwable;

class CreateVideoUseCaseTest extends BaseVideoUseCase
{

    public function getUseCase(): string
    {
        return CreateVideoUseCase::class;
    }

    public function inputDTO(array  $categories = [],
                             array  $genres = [],
                             array  $castMembers = [],
                             ?array $videoFile = null,
                             ?array $trailerFile = null,
                             ?array $bannerFile = null,
                             ?array $thumbFile = null,
                             ?array $thumbHalf = null,
    ): object
    {
        return new VideoCreateInputDto(
            title: 'test',
            description: 'test',
            yearLaunched: 2023,
            duration: 50,
            opened: true,
            rating: Rating::RATE10,
            categoriesId: $categories,
            genresId: $genres,
            castMembersId: $castMembers,
            videoFile: $videoFile,
            trailerFile: $trailerFile,
            thumbFile: $bannerFile,
            thumbHalf: $thumbFile,
            bannerFile: $thumbHalf,
        );
    }

    public function testTransactionException()
    {
        Event::listen(TransactionBeginning::class, fn() => throw new Exception('begin transaction'));
        try {
            $sut = $this->makeSut();
            $sut->execute($this->inputDTO());
            $this->fail();
        } catch (Throwable $th) {
            $this->assertDatabaseCount('videos', 0);
        }
    }

    public function testUploadFilesException()
    {
        Event::listen(UploadFilesStub::class, fn() => throw new Exception());

        try {
            $sut = $this->makeSut();
            $input = $this->inputDTO(videoFile: [
                'tmp_name' => 'video.mp4',
                'name' => 'video.mp4',
                'type' => 'tmp/video.mp4',
                'error' => 0,
            ]);
            $sut->execute($input);

            $this->fail();
        } catch (Throwable $th) {
            $this->assertDatabaseCount('videos', 0);
        }
    }

    public function testEventException()
    {
        Event::listen(VideoEventStub::class, fn() => throw new Exception());

        try {
            $sut = $this->makeSut();
            $input = $this->inputDTO(videoFile: [
                'tmp_name' => 'video.mp4',
                'name' => 'video.mp4',
                'type' => 'tmp/video.mp4',
                'error' => 0,
            ]);
            $sut->execute($input);

            $this->fail();
        } catch (Throwable $th) {
            $this->assertDatabaseCount('videos', 0);
        }
    }
}
