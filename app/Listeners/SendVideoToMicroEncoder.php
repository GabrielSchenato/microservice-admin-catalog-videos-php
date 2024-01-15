<?php

namespace App\Listeners;

use App\Services\AMQP\AMQPInterface;

class SendVideoToMicroEncoder
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly AMQPInterface $amqp)
    {

    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $this->amqp->producerFanout(
            payload: $event->getPayload(),
            exchange: config('microservices.micro_encoder_go.exchange')
        );
    }
}
