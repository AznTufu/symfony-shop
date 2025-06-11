<?php

namespace App\EventListener;

use App\Event\UserUpdatedEvent;
use App\Message\UserNotification;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;

final class UserEventListener
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {}

    #[AsEventListener]
    public function onUserUpdated(UserUpdatedEvent $event): void
    {
        $user = $event->user;

        $this->messageBus->dispatch(new UserNotification(
            $user->getId(),
            $user->getEmail(),
            'updated'
        ));
    }
}