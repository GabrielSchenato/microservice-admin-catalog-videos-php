<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\UpdateCastMember\CastMemberUpdateInputDto;
use Core\UseCase\DTO\CastMember\UpdateCastMember\CastMemberUpdateOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class UpdateCastMemberUseCaseUnitTest extends TestCase
{
    public function testRenameCastMember()
    {
        $castMemberNewName = 'New Cast Member';
        $uuid = RamseyUuid::uuid4()->toString();
        $castMemberName = 'Old Cast Member';
        $castMemberType = CastMemberType::ACTOR;
        $mockEntity = Mockery::mock(CastMemberEntity::class, [
            $castMemberName,
            $castMemberType,
            new Uuid($uuid),
        ]);
        $mockEntity->shouldReceive('update');
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('findById')
            ->once()
            ->with($uuid)
            ->andReturn($mockEntity);
        $mockRepository
            ->shouldReceive('update')
            ->once()
            ->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(CastMemberUpdateInputDto::class, [
            $uuid,
            $castMemberNewName,
        ]);

        $useCase = new UpdateCastMemberUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CastMemberUpdateOutputDto::class, $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
