<?php

namespace Tests\Unit\App\Models;

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

abstract class ModelTestCase extends TestCase
{
    abstract protected function model(): Model;

    abstract protected function traits(): array;

    abstract protected function fillables(): array;

    abstract protected function casts(): array;

    public function testIfUseTraits(): void
    {
        $expected = $this->traits();
        $actual = array_keys(class_uses($this->model()));

        $this->assertEquals($expected, $actual);
    }

    public function testFillables(): void
    {
        $expected = $this->fillables();
        $actual = $this->model()->getFillable();

        $this->assertEquals($expected, $actual);
    }

    public function testIncrementingIsFalse(): void
    {
        $model = $this->model();

        $this->assertFalse($model->incrementing);
    }

    public function testHasCasts(): void
    {
        $expected = $this->casts();
        $actual = $this->model()->getCasts();

        $this->assertEquals($expected, $actual);
    }

}
