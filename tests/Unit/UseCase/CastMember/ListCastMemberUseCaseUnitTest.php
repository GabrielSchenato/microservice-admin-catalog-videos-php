<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\UseCase\DTO\CastMember\CastMemberOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class ListCastMemberUseCaseUnitTest extends TestCase
{
    public function testGetById()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $castMemberName = 'New Cast Member';
        $mockEntity = Mockery::mock(CastMemberEntity::class, [
            $castMemberName,
            CastMemberType::DIRECTOR,
            new Uuid($uuid)
        ]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('findById')
            ->once()
            ->with($uuid)
            ->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(CastMemberInputDto::class, [
            $uuid
        ]);

        $useCase = new ListCastMemberUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CastMemberOutputDto::class, $response);
        $this->assertEquals($castMemberName, $response->name);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
