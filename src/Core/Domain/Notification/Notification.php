<?php

namespace Core\Domain\Notification;

class Notification
{
    private array $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function addError(array $error): void
    {
        $this->errors[] = $error;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) != 0;
    }

    public function messages(string $context = ''): string
    {
        $messages = '';
        foreach ($this->errors as $error) {
            if ($context === '' || $error['context'] == $context) {
                $messages .= "{$error['context']}: {$error['message']}, ";
            }
        }

        return $messages;
    }
}
