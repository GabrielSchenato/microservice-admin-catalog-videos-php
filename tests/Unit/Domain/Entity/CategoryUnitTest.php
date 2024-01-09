<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\CategoryEntity;
use Core\Domain\Exception\EntityValidationException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Throwable;

class CategoryUnitTest extends TestCase
{
    public function testAttributes()
    {
        $category = new CategoryEntity(
            name: 'New Cat',
            description: 'New desc',
            isActive: true
        );

        $this->assertNotEmpty($category->id());
        $this->assertNotEmpty($category->createdAt());
        $this->assertEquals('New Cat', $category->name);
        $this->assertEquals('New desc', $category->description);
        $this->assertTrue($category->isActive);
    }

    public function testActivated()
    {
        $category = new CategoryEntity(
            name: 'New Cat',
            isActive: false
        );

        $this->assertFalse($category->isActive);
        $category->activate();
        $this->assertTrue($category->isActive);
    }

    public function testDisabled()
    {
        $category = new CategoryEntity(
            name: 'New Cat'
        );

        $this->assertTrue($category->isActive);
        $category->disabled();
        $this->assertFalse($category->isActive);
    }

    public function testUpdate()
    {
        $uuid = Uuid::uuid4()->toString();
        $createdAt = '2024-01-01 12:00:00';

        $category = new CategoryEntity(
            id: $uuid,
            name: 'Old Cat',
            description: 'Old desc',
            isActive: true,
            createdAt: $createdAt
        );
        $category->update(
            name: 'New Cat',
            description: 'New desc'
        );

        $this->assertEquals($uuid, $category->id());
        $this->assertEquals($createdAt, $category->createdAt());
        $this->assertEquals('New Cat', $category->name);
        $this->assertEquals('New desc', $category->description);
    }

    public function testExceptionName()
    {
        try {
            new CategoryEntity(
                name: 'Na',
                description: 'New desc'
            );

            $this->fail();
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }

    public function testExceptionDescription()
    {
        try {
            new CategoryEntity(
                name: 'New Cat',
                description: random_bytes(300)
            );

            $this->fail();
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }
}