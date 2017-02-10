<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance;

use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\CartBoxPage;
use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\CartPage;
use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\FloatingWindowPage;
use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\HomepagePage;
use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\ProductDetailPage;
use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\ProductListPage;
use Shopsys\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class CartCest
{
    public function testAddingSameProductToCartMakesSum(
        CartPage $cartPage,
        ProductDetailPage $productDetailPage,
        CartBoxPage $cartBoxPage,
        AcceptanceTester $me,
        FloatingWindowPage $floatingWindowPage
    ) {
        $me->wantTo('have more pieces of the same product as one item in cart');
        $me->amOnPage('/22-sencor-sle-22f46dm4-hello-kitty/');

        $productDetailPage->addProductIntoCart(3);
        $floatingWindowPage->closeFloatingWindow();
        $cartBoxPage->seeInCartBox('1 položka za 10 497,00 Kč');

        $productDetailPage->addProductIntoCart(3);
        $floatingWindowPage->closeFloatingWindow();
        $cartBoxPage->seeInCartBox('1 položka za 20 994,00 Kč');

        $me->amOnPage('/kosik/');

        $cartPage->assertProductQuantity('22" Sencor SLE 22F46DM4 HELLO KITTY', 6);
    }

    public function testAddToCartFromProductListPage(
        CartPage $cartPage,
        ProductListPage $productListPage,
        CartBoxPage $cartBoxPage,
        AcceptanceTester $me,
        FloatingWindowPage $floatingWindowPage
    ) {
        $me->wantTo('add product to cart from product list');
        $me->amOnPage('/televize-audio/');
        $productListPage->addProductToCartByName('Defender 2.0 SPK-480', 1);
        $me->see('Do košíku bylo vloženo zboží');
        $floatingWindowPage->closeFloatingWindow();
        $cartBoxPage->seeInCartBox('1 položka');
        $me->amOnPage('/kosik/');
        $cartPage->assertProductPrice('Defender 2.0 SPK-480', '119,00 Kč');
    }

    public function testAddToCartFromHomepage(
        CartPage $cartPage,
        HomepagePage $homepagePage,
        CartBoxPage $cartBoxPage,
        AcceptanceTester $me,
        FloatingWindowPage $floatingWindowPage
    ) {
        $me->wantTo('add product to cart from homepage');
        $me->amOnPage('/');
        $homepagePage->addTopProductToCartByName('22" Sencor SLE 22F46DM4 HELLO KITTY', 1);
        $me->see('Do košíku bylo vloženo zboží');
        $floatingWindowPage->closeFloatingWindow();
        $cartBoxPage->seeInCartBox('1 položka');
        $me->amOnPage('/kosik/');
        $cartPage->assertProductPrice('22" Sencor SLE 22F46DM4 HELLO KITTY', '3 499,00 Kč');
    }

    public function testAddToCartFromProductDetail(
        ProductDetailPage $productDetailPage,
        CartBoxPage $cartBoxPage,
        AcceptanceTester $me,
        FloatingWindowPage $floatingWindowPage
    ) {
        $me->wantTo('add product to cart from product detail');
        $me->amOnPage('/22-sencor-sle-22f46dm4-hello-kitty/');
        $me->see('Vložit do košíku');
        $productDetailPage->addProductIntoCart(3);
        $me->see('Do košíku bylo vloženo zboží');
        $floatingWindowPage->closeFloatingWindow();
        $cartBoxPage->seeInCartBox('1 položka za 10 497,00 Kč');
        $me->amOnPage('/kosik/');
        $me->see('22" Sencor SLE 22F46DM4 HELLO KITTY');
    }

    public function testChangeCartItemAndRecalculatePrice(
        CartPage $cartPage,
        ProductDetailPage $productDetailPage,
        AcceptanceTester $me
    ) {
        $me->wantTo('change items in cart and recalculate price');
        $me->amOnPage('/22-sencor-sle-22f46dm4-hello-kitty/');
        $me->see('Vložit do košíku');
        $productDetailPage->addProductIntoCart(3);
        $me->clickByText('Přejít do košíku');

        $cartPage->changeProductQuantity('22" Sencor SLE 22F46DM4 HELLO KITTY', 10);
        $cartPage->assertTotalPriceWithVat('34 990,00 Kč');
    }

    public function testRemovingItemsFromCart(
        CartPage $cartPage,
        ProductDetailPage $productDetailPage,
        AcceptanceTester $me
    ) {
        $me->wantTo('add some items to cart and remove them');

        $me->amOnPage('/kniha-bodovy-system-a-pravidla-silnicniho-provozu/');
        $productDetailPage->addProductIntoCart();
        $me->amOnPage('/jura-impressa-j9-tft-carbon/');
        $productDetailPage->addProductIntoCart();

        $me->amOnPage('/kosik/');
        $cartPage->assertProductIsInCartByName('JURA Impressa J9 TFT Carbon');
        $cartPage->assertProductIsInCartByName('Kniha Bodový systém a pravidla silničního provozu');

        $cartPage->removeProductFromCart('JURA Impressa J9 TFT Carbon');
        $cartPage->assertProductIsNotInCartByName('JURA Impressa J9 TFT Carbon');

        $cartPage->removeProductFromCart('Kniha Bodový systém a pravidla silničního provozu');
        $me->see('Váš nákupní košík je bohužel prázdný.');
    }

    public function testAddingDistinctProductsToCart(
        CartPage $cartPage,
        CartBoxPage $cartBoxPage,
        ProductDetailPage $productDetailPage,
        AcceptanceTester $me,
        FloatingWindowPage $floatingWindowPage
    ) {
        $me->wantTo('add distinct products to cart');

        $me->amOnPage('/22-sencor-sle-22f46dm4-hello-kitty/');
        $productDetailPage->addProductIntoCart();
        $floatingWindowPage->closeFloatingWindow();
        $cartBoxPage->seeInCartBox('1 položka za 3 499,00 Kč');

        $me->amOnPage('/canon-pixma-ip7250/');
        $productDetailPage->addProductIntoCart();
        $floatingWindowPage->closeFloatingWindow();
        $cartBoxPage->seeInCartBox('2 položky za 27 687,00 Kč');

        $me->amOnPage('/kosik/');
        $cartPage->assertProductIsInCartByName('22" Sencor SLE 22F46DM4 HELLO KITTY');
        $cartPage->assertProductIsInCartByName('Canon PIXMA iP7250');
    }

    public function testPricingInCart(
        CartPage $cartPage,
        ProductDetailPage $productDetailPage,
        AcceptanceTester $me
    ) {
        $me->wantTo('see that prices of products in cart are calculated well');

        $me->amOnPage('/aquila-aquagym-pramenita-voda-neperliva/');
        $productDetailPage->addProductIntoCart(10);
        $me->amOnPage('/stokorunova-poukazka/');
        $productDetailPage->addProductIntoCart(100);
        $me->amOnPage('/premiumcord-micro-usb-a-b-1m/');
        $productDetailPage->addProductIntoCart(75);

        $me->amOnPage('/kosik/');
        $cartPage->assertTotalPriceWithVat('17 350,00 Kč');
    }
}
