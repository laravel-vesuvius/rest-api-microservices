<?php

declare(strict_types=1);

namespace App\Application\Utils;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class MessengerUtils
{
    /**
     * @param Envelope $envelope
     * @return mixed
     */
    public static function getResultFromEnvelope(Envelope $envelope)
    {
        return $envelope->last(HandledStamp::class)->getResult();
    }
}
