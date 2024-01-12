<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

class CastMemberEntityUnitTest extends TestCase
{

    public function testAttributesCreate()
    {
        $name = 'New CastMember';
        $castMember = new CastMemberEntity(
            name: $name,
            type: CastMemberType::ACTOR
        );

        $this->assertNotEmpty($castMember->id());
        $this->assertEquals($name, $castMember->name);
        $this->assertEquals(CastMemberType::ACTOR, $castMember->type);
        $this->assertNotEmpty($castMember->createdAt());
    }

    public function testAttributesUpdate()
    {
        $uuid = RamseyUuid::uuid4();
        $name = 'New CastMember';
        $date = date('Y-m-d H:i:s');
        $castMember = new CastMemberEntity(
            name: $name,
            type: CastMemberType::ACTOR,
            id: new Uuid($uuid),
            createdAt: new DateTime($date)
        );

        $this->assertEquals($uuid, $castMember->id());
        $this->assertEquals($name, $castMember->name);
        $this->assertEquals($date, $castMember->createdAt());
    }

    public function testUpdate()
    {
        $uuid = new Uuid(RamseyUuid::uuid4()->toString());
        $createdAt = '2024-01-01 12:00:00';

        $castMember = new CastMemberEntity(
            name: 'Old CastMember',
            type: CastMemberType::ACTOR,
            id: $uuid,
            createdAt: new DateTime($createdAt)
        );
        $castMember->update(
            name: 'New CastMember'
        );

        $this->assertEquals($uuid, $castMember->id());
        $this->assertEquals($createdAt, $castMember->createdAt());
        $this->assertEquals('New CastMember', $castMember->name);
    }

    public function testExceptionName()
    {
        $this->expectException(EntityValidationException::class);
        new CastMemberEntity(
            name: 'Na',
            type: CastMemberType::ACTOR
        );
    }
}
