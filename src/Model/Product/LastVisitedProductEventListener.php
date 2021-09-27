<?php

namespace App\Model\Product;

use Symfony\Component\HttpKernel\Event\KernelEvent;

class LastVisitedProductEventListener
{
    public function onKernelResponse(KernelEvent $event)
    {
        if ($event->getRequest()->attributes->get('_route') === 'front_product_detail') {
            $productId = $event->getRequest()->attributes->get('id');
            $lastVisitedProduct = new LastVisitedProduct();
            $ids = $lastVisitedProduct->getLastVisitedProduct();

            foreach ($ids as $key => $value) {
                if ($productId === $value) {
                    unset($ids[$key]);
                }
            }

            array_unshift($ids, $productId);
            array_splice($ids, 5);
            setcookie("visitedProduct", json_encode($ids), strtotime( '+3 months' ), '/');
        }
    }
}
