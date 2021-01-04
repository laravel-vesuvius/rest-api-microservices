<?php

declare(strict_types=1);

namespace App\EventListener\Kernel;

use App\Service\TokenManager;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TokenResolverListener
{
    /**
     * @var TokenManager
     */
    private $tokenManager;

    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $token = $request->headers->has('Authorization')
            ? str_replace('Bearer ', '', $request->headers->get('Authorization'))
            : null;
        if (!$token) {
            return;
        }

        $this->tokenManager->setTokenToStorage($token);
    }
}
