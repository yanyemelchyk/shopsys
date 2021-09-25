<?php

declare(strict_types=1);

namespace App\Model\Product;

class LastVisitedProduct
{
    public function getLastVisitedProduct()
    {
        if (isset($_COOKIE['visitedProduct'])) {
            return json_decode($_COOKIE['visitedProduct']);
        }

        return [];
    }
}
