<?php

namespace SS6\ShopBundle\Tests\Database\Model\Order;

use SS6\ShopBundle\Component\DataFixture\PersistentReferenceFacade;
use SS6\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use SS6\ShopBundle\DataFixtures\Base\OrderStatusDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\CountryDataFixture;
use SS6\ShopBundle\Model\Cart\CartFacade;
use SS6\ShopBundle\Model\Cart\CartService;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Order\Item\OrderItemData;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderFacade;
use SS6\ShopBundle\Model\Order\OrderRepository;
use SS6\ShopBundle\Model\Order\Preview\OrderPreviewFactory;
use SS6\ShopBundle\Model\Payment\PaymentRepository;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Transport\TransportRepository;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class OrderFacadeTest extends DatabaseTestCase {

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function testCreate() {
		$cartFacade = $this->getContainer()->get(CartFacade::class);
		/* @var $cartFacade \SS6\ShopBundle\Model\Cart\CartFacade */
		$cartService = $this->getContainer()->get(CartService::class);
		/* @var $cartService \SS6\ShopBundle\Model\Cart\CartService */
		$orderFacade = $this->getContainer()->get(OrderFacade::class);
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */
		$orderPreviewFactory = $this->getContainer()->get(OrderPreviewFactory::class);
		/* @var $orderPreviewFactory \SS6\ShopBundle\Model\Order\Preview\OrderPreviewFactory */
		$orderRepository = $this->getContainer()->get(OrderRepository::class);
		/* @var $orderRepository \SS6\ShopBundle\Model\Order\OrderRepository */
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$transportRepository = $this->getContainer()->get(TransportRepository::class);
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */
		$paymentRepository = $this->getContainer()->get(PaymentRepository::class);
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */
		$persistentReferenceFacade = $this->getContainer()->get(PersistentReferenceFacade::class);
		/* @var $persistentReferenceFacade \SS6\ShopBundle\Component\DataFixture\PersistentReferenceFacade */

		$cart = $cartFacade->getCartOfCurrentCustomer();

		$customerIdentifier = new CustomerIdentifier('randomString');

		$product = $productRepository->getById(1);

		$cartService->addProductToCart($cart, $customerIdentifier, $product, 1);

		$transport = $transportRepository->getById(1);
		$payment = $paymentRepository->getById(1);

		$orderData = new OrderData();
		$orderData->transport = $transport;
		$orderData->payment = $payment;
		$orderData->status = $persistentReferenceFacade->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
		$orderData->firstName = 'firstName';
		$orderData->lastName = 'lastName';
		$orderData->email = 'email';
		$orderData->telephone = 'telephone';
		$orderData->companyName = 'companyName';
		$orderData->companyNumber = 'companyNumber';
		$orderData->companyTaxNumber = 'companyTaxNumber';
		$orderData->street = 'street';
		$orderData->city = 'city';
		$orderData->postcode = 'postcode';
		$orderData->country = $persistentReferenceFacade->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
		$orderData->deliveryAddressSameAsBillingAddress = false;
		$orderData->deliveryFirstName = 'deliveryFirstName';
		$orderData->deliveryLastName = 'deliveryLastName';
		$orderData->deliveryCompanyName = 'deliveryCompanyName';
		$orderData->deliveryTelephone = 'deliveryTelephone';
		$orderData->deliveryStreet = 'deliveryStreet';
		$orderData->deliveryCity = 'deliveryCity';
		$orderData->deliveryPostcode = 'deliveryPostcode';
		$orderData->deliveryCountry = $persistentReferenceFacade->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
		$orderData->note = 'note';
		$orderData->domainId = 1;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);

		$orderPreview = $orderPreviewFactory->createForCurrentUser($transport, $payment);
		$order = $orderFacade->createOrder($orderData, $orderPreview, null);

		$orderFromDb = $orderRepository->getById($order->getId());

		$this->assertSame($orderData->transport->getId(), $orderFromDb->getTransport()->getId());
		$this->assertSame($orderData->payment->getId(), $orderFromDb->getPayment()->getId());
		$this->assertSame($orderData->firstName, $orderFromDb->getFirstName());
		$this->assertSame($orderData->lastName, $orderFromDb->getLastName());
		$this->assertSame($orderData->email, $orderFromDb->getEmail());
		$this->assertSame($orderData->telephone, $orderFromDb->getTelephone());
		$this->assertSame($orderData->companyName, $orderFromDb->getCompanyName());
		$this->assertSame($orderData->companyNumber, $orderFromDb->getCompanyNumber());
		$this->assertSame($orderData->companyTaxNumber, $orderFromDb->getCompanyTaxNumber());
		$this->assertSame($orderData->street, $orderFromDb->getStreet());
		$this->assertSame($orderData->city, $orderFromDb->getCity());
		$this->assertSame($orderData->postcode, $orderFromDb->getPostcode());
		$this->assertSame($orderData->country, $orderFromDb->getCountry());
		$this->assertSame($orderData->deliveryFirstName, $orderFromDb->getDeliveryFirstName());
		$this->assertSame($orderData->deliveryLastName, $orderFromDb->getDeliveryLastName());
		$this->assertSame($orderData->deliveryCompanyName, $orderFromDb->getDeliveryCompanyName());
		$this->assertSame($orderData->deliveryTelephone, $orderFromDb->getDeliveryTelephone());
		$this->assertSame($orderData->deliveryStreet, $orderFromDb->getDeliveryStreet());
		$this->assertSame($orderData->deliveryCity, $orderFromDb->getDeliveryCity());
		$this->assertSame($orderData->deliveryPostcode, $orderFromDb->getDeliveryPostcode());
		$this->assertSame($orderData->deliveryCountry, $orderFromDb->getDeliveryCountry());
		$this->assertSame($orderData->note, $orderFromDb->getNote());
		$this->assertSame($orderData->domainId, $orderFromDb->getDomainId());

		$this->assertCount(3, $orderFromDb->getItems());
	}

	public function testEdit() {
		$orderFacade = $this->getContainer()->get(OrderFacade::class);
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */
		$orderRepository = $this->getContainer()->get(OrderRepository::class);
		/* @var $orderRepository \SS6\ShopBundle\Model\Order\OrderRepository */

		$order = $this->getReference('order_1');
		/* @var $order \SS6\ShopBundle\Model\Order\Order */

		$this->assertCount(4, $order->getItems());

		$orderData = new OrderData();
		$orderData->setFromEntity($order);

		$orderItemsData = $orderData->itemsWithoutTransportAndPayment;
		array_pop($orderItemsData);

		$orderItemData1 = new OrderItemData();
		$orderItemData1->name = 'itemName1';
		$orderItemData1->priceWithoutVat = 100;
		$orderItemData1->priceWithVat = 121;
		$orderItemData1->vatPercent = 21;
		$orderItemData1->quantity = 3;

		$orderItemData2 = new OrderItemData();
		$orderItemData2->name = 'itemName2';
		$orderItemData2->priceWithoutVat = 333;
		$orderItemData2->priceWithVat = 333;
		$orderItemData2->vatPercent = 0;
		$orderItemData2->quantity = 1;

		$orderItemsData[OrderData::NEW_ITEM_PREFIX . '1'] = $orderItemData1;
		$orderItemsData[OrderData::NEW_ITEM_PREFIX . '2'] = $orderItemData2;

		$orderData->itemsWithoutTransportAndPayment = $orderItemsData;
		$orderFacade->edit($order->getId(), $orderData);

		$orderFromDb = $orderRepository->getById($order->getId());

		$this->assertCount(5, $orderFromDb->getItems());
	}

}
