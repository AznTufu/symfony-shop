<?php

namespace App\MessageHandler;

use App\Entity\Notification;
use App\Entity\User;
use App\Message\AddPointsToUsersMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AddPointsToUsersMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(AddPointsToUsersMessage $message): void
    {
        $users = $this->entityManager->getRepository(User::class)
            ->findBy(['active' => true]);
        
        foreach ($users as $user) {
            $currentPoints = $user->getPoints() ?: 0;
            $user->setPoints($currentPoints + $message->points);

            $notification = new Notification();
            $notification->setLabel(sprintf(
                'Félicitations ! Vous avez reçu %d points bonus !',
                $message->points
            ));
            $notification->setUser($user);
            
            $this->entityManager->persist($notification);
        }

        $this->entityManager->flush();
    }
}