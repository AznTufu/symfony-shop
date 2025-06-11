<?php

namespace App\Message;

final class ProductNotification
{
    public const TYPE_CREATED = 'created';
    public const TYPE_UPDATED = 'updated';
    public const TYPE_DELETED = 'deleted';

    public function __construct(
        public readonly int $productId,
        public readonly string $productName,
        public readonly int $userId,
        public readonly string $type
    ) {}
}