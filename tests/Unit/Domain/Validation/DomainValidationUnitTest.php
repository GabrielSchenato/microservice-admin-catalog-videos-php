<?php

namespace Tests\Unit\Domain\Validation;

use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Validation\DomainValidation;
use PHPUnit\Framework\TestCase;
use Throwable;

class DomainValidationUnitTest extends TestCase
{
    public function testNotNull()
    {
        try {
            $value = '';
            DomainValidation::notNull($value);
            $this->fail();
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }

    public function testNotNullCustomMessageException()
    {
        try {
            $value = '';
            $error = 'custom message error';
            DomainValidation::notNull($value, $error);
            $this->fail();
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, $error);
        }
    }

    public function testStrMaxLength()
    {
        try {
            $value = 'teste';
            $error = 'custom message error';
            DomainValidation::strMaxLength($value, 3, $error);
            $this->fail();
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, $error);
        }
    }

    public function testStrMinLength()
    {
        try {
            $value = 'test';
            $error = 'custom message error';
            DomainValidation::strMinLength($value, 8, $error);
            $this->fail();
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, $error);
        }
    }

    public function testStrCanNullAndMaxLength()
    {
        try {
            $value = 'teste';
            $error = 'custom message error';
            DomainValidation::strCanNullAndMaxLength($value, 3, $error);
            $this->fail();
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, $error);
        }
    }
}
