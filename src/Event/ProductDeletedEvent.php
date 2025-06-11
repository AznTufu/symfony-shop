<?php

namespace App\Event;

use App\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductDeletedEvent extends Event
{
    public function __construct(
        public readonly Product $product
    ) {}
}