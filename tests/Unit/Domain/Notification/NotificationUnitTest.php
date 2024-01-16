<?php

namespace Tests\Unit\Domain\Notification;

use Core\Domain\Notification\Notification;
use PHPUnit\Framework\TestCase;

class NotificationUnitTest extends TestCase
{
    public function testGetErrors()
    {
        $notification = new Notification();
        $errors = $notification->getErrors();

        $this->assertIsArray($errors);
    }

    public function testAddErrors()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'video',
            'message' => 'video title is required',
        ]);

        $errors = $notification->getErrors();

        $this->assertCount(1, $errors);
    }

    public function testHasErrors()
    {
        $notification = new Notification();

        $this->assertFalse($notification->hasErrors());

        $notification->addError([
            'context' => 'video',
            'message' => 'video title is required',
        ]);

        $this->assertTrue($notification->hasErrors());
    }

    public function testMessage()
    {
        $notification = new Notification();

        $notification->addError([
            'context' => 'video',
            'message' => 'video title is required',
        ]);
        $notification->addError([
            'context' => 'video',
            'message' => 'description is required',
        ]);

        $messages = $notification->messages();

        $this->assertIsString($messages);
        $this->assertEquals(expected: 'video: video title is required, video: description is required, ', actual: $messages);
    }

    public function testMessageFilterContext()
    {
        $notification = new Notification();

        $notification->addError([
            'context' => 'video',
            'message' => 'video title is required',
        ]);
        $notification->addError([
            'context' => 'category',
            'message' => 'name is required',
        ]);
        $this->assertCount(2, $notification->getErrors());
        $messages = $notification->messages(context: 'video');
        $this->assertIsString($messages);
        $this->assertEquals(expected: 'video: video title is required, ', actual: $messages);
    }
}
