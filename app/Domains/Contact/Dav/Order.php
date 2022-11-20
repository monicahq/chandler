<?php

namespace App\Domains\Contact\Dav;

use Attribute;

#[Attribute]
class Order
{
    public function __construct(
        public int $order,
    ) {
    }
}
