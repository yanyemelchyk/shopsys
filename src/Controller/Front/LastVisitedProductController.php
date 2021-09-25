<?php

declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Product\LastVisitedProduct;
use Shopsys\ReadModelBundle\Product\Detail\ProductDetailViewFacade;

class LastVisitedProductController extends FrontBaseController
{
    public function showProductList(ProductDetailViewFacade $productDetailViewFacade)
    {
        $lastVisitedProduct = new LastVisitedProduct();
        $ids = $lastVisitedProduct->getLastVisitedProduct();
        $productDetailViews = [];

        foreach ($ids as $id) {
            $productDetailViews[] = $productDetailViewFacade->getVisibleProductDetail($id);
        }

        return $this->render('Front/Content/Product/showLastVisited.html.twig', ['productViews' => $productDetailViews]);
    }
}
