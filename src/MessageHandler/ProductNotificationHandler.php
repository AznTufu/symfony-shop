<?php

namespace App\MessageHandler;

use App\Entity\Notification;
use App\Entity\User;
use App\Message\ProductNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ProductNotificationHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(ProductNotification $message): void
    {
        $user = $this->entityManager->getRepository(User::class)->find($message->userId);
    
        if (!$user) {
            return;
        }

        $action = match($message->type) {
            ProductNotification::TYPE_CREATED => 'ajouté',
            ProductNotification::TYPE_UPDATED => 'modifié',
            ProductNotification::TYPE_DELETED => 'supprimé',
            default => 'modifié'
        };

        $notification = new Notification();
        $notification->setLabel(sprintf(
            'Produit %s : %s (ID: %d)',
            $action,
            $message->productName,
            $message->productId
        ));
        $notification->setUser($user);
    
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}