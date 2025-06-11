<?php

namespace App\MessageHandler;

use App\Entity\Notification;
use App\Entity\User;
use App\Message\UserNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UserNotificationHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(UserNotification $message): void
    {
        $user = $this->entityManager->getRepository(User::class)->find($message->userId);
    
        if (!$user) {
            return;
        }

        $notification = new Notification();
        $notification->setLabel(sprintf(
            'Utilisateur mis à jour : %s (ID: %d)',
            $message->userEmail,
            $message->userId
        ));
        $notification->setUser($user);
    
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}