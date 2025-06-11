<?php

namespace App\EventListener;

use App\Event\ProductCreatedEvent;
use App\Event\ProductDeletedEvent;
use App\Event\ProductUpdatedEvent;
use App\Message\ProductNotification;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductEventListener
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {}

    #[AsEventListener]
    public function onProductCreated(ProductCreatedEvent $event): void
    {
        $product = $event->product;

        $this->messageBus->dispatch(new ProductNotification(
            $product->getId(),
            $product->getName(),
            $product->getUser()->getId(),
            ProductNotification::TYPE_CREATED
        ));
    }

    #[AsEventListener]
    public function onProductUpdated(ProductUpdatedEvent $event): void
    {
        $product = $event->product;

        $this->messageBus->dispatch(new ProductNotification(
            $product->getId(),
            $product->getName(),
            $product->getUser()->getId(),
            ProductNotification::TYPE_UPDATED
        ));
    }

    #[AsEventListener]
    public function onProductDeleted(ProductDeletedEvent $event): void
    {
        $product = $event->product;

        $this->messageBus->dispatch(new ProductNotification(
            $product->getId(),
            $product->getName(),
            $product->getUser()->getId(),
            ProductNotification::TYPE_DELETED
        ));
    }
}