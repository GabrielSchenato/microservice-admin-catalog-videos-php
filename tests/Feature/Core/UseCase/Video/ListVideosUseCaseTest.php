<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\Video;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\ListPaginate\DTO\VideosListInputDto;
use Core\UseCase\Video\ListPaginate\ListVideosUseCase;
use Tests\TestCase;


class ListVideosUseCaseTest extends TestCase
{

    /**
     * @dataProvider provider
     */
    public function testPagination(
        int $total,
        int $perPage,
    )
    {
        Video::factory()->count($total)->create();

        $useCase = new ListVideosUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );

        $response = $useCase->execute(new VideosListInputDto(
            filter: '',
            order: 'desc',
            page: 1,
            totalPage: $perPage
        ));

        $this->assertCount($perPage, $response->items());
        $this->assertEquals($total, $response->total());
    }

    public static function provider(): array
    {
        return [
            [
                'total' => 30,
                'perPage' => 10,
            ], [
                'total' => 20,
                'perPage' => 5,
            ], [
                'total' => 0,
                'perPage' => 0,
            ],
        ];
    }

}
