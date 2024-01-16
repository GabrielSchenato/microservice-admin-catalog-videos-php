<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\GenreEntity;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

class GenreEntityUnitTest extends TestCase
{
    public function testAttributesCreate()
    {
        $name = 'New Genre';
        $genre = new GenreEntity(
            name: $name,
        );

        $this->assertNotEmpty($genre->id());
        $this->assertEquals($name, $genre->name);
        $this->assertTrue($genre->isActive);
        $this->assertNotEmpty($genre->createdAt());
    }

    public function testAttributesUpdate()
    {
        $uuid = RamseyUuid::uuid4();
        $name = 'New Genre';
        $date = date('Y-m-d H:i:s');
        $genre = new GenreEntity(
            name: $name,
            id: new Uuid($uuid),
            isActive: true,
            createdAt: new DateTime($date)
        );

        $this->assertEquals($uuid, $genre->id());
        $this->assertEquals($name, $genre->name);
        $this->assertTrue($genre->isActive);
        $this->assertEquals($date, $genre->createdAt());
    }

    public function testActivated()
    {
        $genre = new GenreEntity(
            name: 'New Genre',
            isActive: false
        );

        $this->assertFalse($genre->isActive);
        $genre->activate();
        $this->assertTrue($genre->isActive);
    }

    public function testDisabled()
    {
        $genre = new GenreEntity(
            name: 'New Genre'
        );

        $this->assertTrue($genre->isActive);
        $genre->disabled();
        $this->assertFalse($genre->isActive);
    }

    public function testUpdate()
    {
        $uuid = new Uuid(RamseyUuid::uuid4()->toString());
        $createdAt = '2024-01-01 12:00:00';

        $genre = new GenreEntity(
            name: 'Old Genre',
            id: $uuid,
            isActive: true,
            createdAt: new DateTime($createdAt)
        );
        $genre->update(
            name: 'New Genre'
        );

        $this->assertEquals($uuid, $genre->id());
        $this->assertEquals($createdAt, $genre->createdAt());
        $this->assertEquals('New Genre', $genre->name);
    }

    public function testExceptionName()
    {
        $this->expectException(EntityValidationException::class);
        new GenreEntity(
            name: 'Na'
        );
    }

    public function testAddCategoryToGenre()
    {
        $categoryId = RamseyUuid::uuid4();

        $genre = new GenreEntity(
            name: 'New Genre'
        );

        $this->assertIsArray($genre->categoriesId);
        $this->assertCount(0, $genre->categoriesId);

        $genre->addCategory(
            categoryId: $categoryId
        );
        $genre->addCategory(
            categoryId: $categoryId
        );
        $this->assertCount(2, $genre->categoriesId);
    }

    public function testRemoveCategoryToGenre()
    {
        $categoryId1 = RamseyUuid::uuid4();
        $categoryId2 = RamseyUuid::uuid4();

        $genre = new GenreEntity(
            name: 'New Genre',
            categoriesId: [
                $categoryId1,
                $categoryId2,
            ]
        );

        $this->assertCount(2, $genre->categoriesId);

        $genre->removeCategory($categoryId1);

        $this->assertCount(1, $genre->categoriesId);
        $this->assertEquals($genre->categoriesId[1], $categoryId2);
    }
}
