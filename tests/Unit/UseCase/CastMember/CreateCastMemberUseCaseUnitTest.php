<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CreateCastMember\CastMemberCreateInputDto;
use Core\UseCase\DTO\CastMember\CreateCastMember\CastMemberCreateOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class CreateCastMemberUseCaseUnitTest extends TestCase
{
    public function testCreateNewCastMember()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $castMemberName = 'New Cat';
        $mockEntity = Mockery::mock(CastMemberEntity::class, [
            $castMemberName,
            CastMemberType::DIRECTOR,
            new Uuid($uuid)
        ]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('insert')
            ->once()
            ->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(CastMemberCreateInputDto::class, [
            $castMemberName,
            1
        ]);

        $useCase = new CreateCastMemberUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CastMemberCreateOutputDto::class, $response);
        $this->assertEquals($castMemberName, $response->name);
        $this->assertEquals(1, $response->type);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
