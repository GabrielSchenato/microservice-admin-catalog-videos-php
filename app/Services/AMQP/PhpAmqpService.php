<?php

namespace App\Services\AMQP;

use Closure;

class PhpAmqpService implements AMQPInterface
{

    public function producer(string $queue, array $payload, string $exchange): void
    {
        // TODO: Implement producer() method.
    }

    public function producerFanout(string $queue, array $payload, string $exchange): void
    {
        // TODO: Implement producerFanout() method.
    }

    public function consumer(string $queue, string $exchange, Closure $callback): void
    {
        // TODO: Implement consumer() method.
    }
}
