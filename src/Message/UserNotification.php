<?php

namespace App\Message;

final class UserNotification
{
    public const TYPE_UPDATED = 'updated';

    public function __construct(
        public readonly int $userId,
        public readonly string $userEmail,
        public readonly string $type
    ) {}
}