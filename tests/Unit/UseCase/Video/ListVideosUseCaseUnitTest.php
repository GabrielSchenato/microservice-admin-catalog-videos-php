<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Repository\PaginationInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\ListPaginate\DTO\VideosListInputDto;
use Core\UseCase\Video\ListPaginate\ListVideosUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Unit\UseCase\UseCaseTrait;

class ListVideosUseCaseUnitTest extends TestCase
{
    protected PaginationInterface $mockPagination;

    use UseCaseTrait;

    public function testListPaginate()
    {
        $useCase = new ListVideosUseCase($this->getMockRepository());

        $response = $useCase->execute(
            input: $this->getMockInputDTO()
        );

        $this->assertInstanceOf(PaginationInterface::class, $response);
    }

    private function getMockRepository()
    {
        $mocRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mocRepository
            ->shouldReceive('paginate')
            ->once()
            ->andReturn($this->mockPagination());

        return $mocRepository;
    }

    private function getMockInputDto()
    {
        return Mockery::mock(VideosListInputDto::class, [
            '', 'DESC', 1, 15,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
