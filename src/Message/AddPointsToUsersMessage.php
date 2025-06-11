<?php

namespace App\Message;

final class AddPointsToUsersMessage
{
    public function __construct(
        public readonly int $points
    ) {}
}