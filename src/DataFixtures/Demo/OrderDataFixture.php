<?php

declare(strict_types=1);

namespace App\DataFixtures\Demo;

use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser;
use Shopsys\FrameworkBundle\Model\Customer\User\CustomerUserRepository;
use Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct;
use Shopsys\FrameworkBundle\Model\Order\OrderData;
use Shopsys\FrameworkBundle\Model\Order\OrderDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;

class OrderDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    public const ORDER_PREFIX = 'order_';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUserRepository
     */
    private $customerUserRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderFacade
     */
    private $orderFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory
     */
    private $orderPreviewFactory;

    /**
     * @var \App\Model\Order\OrderDataFactory
     */
    private $orderDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUserRepository $customerUserRepository
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderFacade $orderFacade
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory $orderPreviewFactory
     * @param \App\Model\Order\OrderDataFactory $orderDataFactory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade $currencyFacade
     */
    public function __construct(
        CustomerUserRepository $customerUserRepository,
        OrderFacade $orderFacade,
        OrderPreviewFactory $orderPreviewFactory,
        OrderDataFactoryInterface $orderDataFactory,
        Domain $domain,
        CurrencyFacade $currencyFacade
    ) {
        $this->customerUserRepository = $customerUserRepository;
        $this->orderFacade = $orderFacade;
        $this->orderPreviewFactory = $orderPreviewFactory;
        $this->orderDataFactory = $orderDataFactory;
        $this->domain = $domain;
        $this->currencyFacade = $currencyFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->domain->getAll() as $domainConfig) {
            $domainId = $domainConfig->getId();
            if ($domainId === Domain::SECOND_DOMAIN_ID) {
                $this->loadDistinct($domainId);
            } else {
                $this->loadDefault($domainId);
            }
        }
    }

    /**
     * @param int $domainId
     */
    private function loadDefault(int $domainId): void
    {
        $domainDefaultCurrency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);

        /** @var \App\Model\Customer\User\CustomerUser $customerUser */
        $customerUser = $this->customerUserRepository->findCustomerUserByEmailAndDomain(
            'no-reply@shopsys.com',
            $domainId
        );
        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_DONE);
        $orderData->firstName = 'Ji????';
        $orderData->lastName = '??ev????k';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420369554147';
        $orderData->street = 'Prvn?? 1';
        $orderData->city = 'Ostrava';
        $orderData->postcode = '71200';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -3 day'))->setTime(12, 40, 22);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '9' => 2,
                ProductDataFixture::PRODUCT_PREFIX . '10' => 3,
            ],
            $customerUser
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Iva';
        $orderData->lastName = 'Ja??kov??';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420367852147';
        $orderData->street = 'Druh?? 2';
        $orderData->city = 'Ostrava';
        $orderData->postcode = '71300';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -11 day'))->setTime(4, 34, 19);
        $orderData->createdAsAdministrator = $this->getReference(AdministratorDataFixture::ADMINISTRATOR);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '18' => 2,
                ProductDataFixture::PRODUCT_PREFIX . '19' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '20' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '15' => 5,
            ],
            $customerUser
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_CZECH_POST);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Jan';
        $orderData->lastName = 'Adamovsk??';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420725852147';
        $orderData->street = 'T??et?? 3';
        $orderData->city = 'Ostrava';
        $orderData->postcode = '71200';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -3 day'))->setTime(18, 27, 36);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '4' => 6,
                ProductDataFixture::PRODUCT_PREFIX . '11' => 1,
            ],
            $customerUser
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PPL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_IN_PROGRESS);
        $orderData->firstName = 'Iveta';
        $orderData->lastName = 'Prv??';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420606952147';
        $orderData->street = '??tvrt?? 4';
        $orderData->city = 'Ostrava';
        $orderData->postcode = '70030';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -1 day'))->setTime(18, 30, 01);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '1' => 1,
            ],
            $customerUser
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_DONE);
        $orderData->firstName = 'Jana';
        $orderData->lastName = 'Jan????kov??';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420739852148';
        $orderData->street = 'P??t?? 55';
        $orderData->city = 'Ostrava';
        $orderData->postcode = '71200';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -2 day'))->setTime(1, 46, 6);
        $orderData->createdAsAdministrator = $this->getReference(AdministratorDataFixture::SUPERADMINISTRATOR);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '2' => 8,
                ProductDataFixture::PRODUCT_PREFIX . '3' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '1' => 2,
            ],
            $customerUser
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PPL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Dominik';
        $orderData->lastName = 'Ha??ek';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420721852152';
        $orderData->street = '??est?? 39';
        $orderData->city = 'Pardubice';
        $orderData->postcode = '58941';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -12 day'))->setTime(0, 49, 0);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '13' => 2,
                ProductDataFixture::PRODUCT_PREFIX . '14' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '15' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '16' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '17' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '18' => 1,
            ],
            $customerUser
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_CANCELED);
        $orderData->firstName = 'Ji????';
        $orderData->lastName = 'Sov??k';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420755872155';
        $orderData->street = 'Sedm?? 1488';
        $orderData->city = 'Opava';
        $orderData->postcode = '85741';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -13 day'))->setTime(23, 35, 15);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '7' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '8' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '12' => 2,
            ]
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_CZECH_POST);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_DONE);
        $orderData->firstName = 'Josef';
        $orderData->lastName = 'Somr';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420369852147';
        $orderData->street = 'Osm?? 1';
        $orderData->city = 'Praha';
        $orderData->postcode = '30258';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -5 day'))->setTime(9, 11, 59);
        $orderData->createdAsAdministrator = $this->getReference(AdministratorDataFixture::SUPERADMINISTRATOR);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '1' => 6,
                ProductDataFixture::PRODUCT_PREFIX . '2' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '12' => 1,
            ]
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_CANCELED);
        $orderData->firstName = 'Ivan';
        $orderData->lastName = 'Horn??k';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420755496328';
        $orderData->street = 'Des??t?? 10';
        $orderData->city = 'Plze??';
        $orderData->postcode = '30010';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -14 day'))->setTime(12, 54, 07);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '9' => 3,
                ProductDataFixture::PRODUCT_PREFIX . '13' => 2,
            ]
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PPL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Adam';
        $orderData->lastName = 'Bo??i??';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420987654321';
        $orderData->street = 'Ciheln?? 5';
        $orderData->city = 'Liberec';
        $orderData->postcode = '65421';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -7 day'))->setTime(7, 2, 31);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '3' => 1,
            ]
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_IN_PROGRESS);
        $orderData->firstName = 'Ev??en';
        $orderData->lastName = 'Farn??';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420456789123';
        $orderData->street = 'Gagarinova 333';
        $orderData->city = 'Hodon??n';
        $orderData->postcode = '69501';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -7 day'))->setTime(11, 28, 20);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '1' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '2' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '3' => 1,
            ]
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_DONE);
        $orderData->firstName = 'Ivana';
        $orderData->lastName = 'Jane??kov??';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420369852147';
        $orderData->street = 'Kalu??n?? 88';
        $orderData->city = 'Lednice';
        $orderData->postcode = '69144';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -10 day'))->setTime(18, 3, 36);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '4' => 2,
                ProductDataFixture::PRODUCT_PREFIX . '3' => 1,
            ]
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_CZECH_POST);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Pavel';
        $orderData->lastName = 'Nov??k';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420605123654';
        $orderData->street = 'Adresn?? 6';
        $orderData->city = 'Opava';
        $orderData->postcode = '72589';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -10 day'))->setTime(23, 47, 11);
        $orderData->createdAsAdministrator = $this->getReference(AdministratorDataFixture::ADMINISTRATOR);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '10' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '20' => 4,
            ]
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PPL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_DONE);
        $orderData->firstName = 'Pavla';
        $orderData->lastName = 'Ad??mkov??';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+4206051836459';
        $orderData->street = 'V??po??etni 16';
        $orderData->city = 'Praha';
        $orderData->postcode = '30015';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -10 day'))->setTime(8, 14, 8);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '15' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '18' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '19' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '3' => 1,
            ]
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_IN_PROGRESS);
        $orderData->firstName = 'Adam';
        $orderData->lastName = '??itn??';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+4206051836459';
        $orderData->street = 'P????m?? 1';
        $orderData->city = 'Plze??';
        $orderData->postcode = '30010';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -11 day'))->setTime(4, 43, 25);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '9' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '19' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '6' => 1,
            ]
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PPL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Radim';
        $orderData->lastName = 'Sv??tek';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420733598748';
        $orderData->street = 'K??iv?? 11';
        $orderData->city = 'Jablonec';
        $orderData->postcode = '78952';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->companyName = 'BestCompanyEver, s.r.o.';
        $orderData->companyNumber = '555555';
        $orderData->note = 'Douf??m, ??e v??e doraz?? v po????dku a co nejd????ve :)';
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -5 day'))->setTime(3, 3, 12);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '7' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '17' => 6,
                ProductDataFixture::PRODUCT_PREFIX . '9' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '14' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '10' => 2,
            ]
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PPL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Radim';
        $orderData->lastName = 'Sv??tek';
        $orderData->email = 'vitek@shopsys.com';
        $orderData->telephone = '+420733598748';
        $orderData->street = 'K??iv?? 11';
        $orderData->city = 'Jablonec';
        $orderData->postcode = '78952';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = false;
        $orderData->deliveryCity = 'Ostrava';
        $orderData->deliveryCompanyName = 'BestCompanyEver, s.r.o.';
        $orderData->deliveryCountry = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA);
        $orderData->deliveryStreet = 'K??iv?? 11';
        $orderData->deliveryTelephone = '+421555444';
        $orderData->deliveryPostcode = '01305';
        $orderData->deliveryFirstName = 'Pavol';
        $orderData->deliveryLastName = 'Sv??tek';
        $orderData->companyName = 'BestCompanyEver, s.r.o.';
        $orderData->companyNumber = '555555';
        $orderData->note = 'Douf??m, ??e v??e doraz?? v po????dku a co nejd????ve :)';
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -13 day'))->setTime(17, 34, 40);
        $orderData->createdAsAdministrator = $this->getReference(AdministratorDataFixture::ADMINISTRATOR);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '7' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '17' => 6,
                ProductDataFixture::PRODUCT_PREFIX . '9' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '14' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '10' => 2,
            ]
        );

        /** @var \App\Model\Customer\User\CustomerUser $customerUser */
        $customerUser = $this->customerUserRepository->findCustomerUserByEmailAndDomain(
            'vitek@shopsys.com',
            $domainId
        );
        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PPL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Radim';
        $orderData->lastName = 'Sv??tek';
        $orderData->email = 'vitek@shopsys.com';
        $orderData->telephone = '+420733598748';
        $orderData->street = 'K??iv?? 11';
        $orderData->city = 'Jablonec';
        $orderData->postcode = '78952';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = false;
        $orderData->deliveryCity = 'Ostrava';
        $orderData->deliveryCompanyName = 'BestCompanyEver, s.r.o.';
        $orderData->deliveryCountry = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA);
        $orderData->deliveryStreet = 'K??iv?? 11';
        $orderData->deliveryTelephone = '+421555444';
        $orderData->deliveryPostcode = '01305';
        $orderData->deliveryFirstName = 'Pavol';
        $orderData->deliveryLastName = 'Sv??tek';
        $orderData->companyName = 'BestCompanyEver, s.r.o.';
        $orderData->companyNumber = '555555';
        $orderData->note = 'Douf??m, ??e v??e doraz?? v po????dku a co nejd????ve :)';
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -6 day'))->setTime(5, 7, 38);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '7' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '17' => 6,
                ProductDataFixture::PRODUCT_PREFIX . '9' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '14' => 1,
                ProductDataFixture::PRODUCT_PREFIX . '10' => 2,
            ],
            $customerUser
        );
    }

    /**
     * @param int $domainId
     */
    private function loadDistinct(int $domainId)
    {
        $domainDefaultCurrency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_CZECH_POST);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_IN_PROGRESS);
        $orderData->firstName = 'V??clav';
        $orderData->lastName = 'Sv??rko??';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420725711368';
        $orderData->street = 'Dev??t?? 25';
        $orderData->city = 'Ostrava';
        $orderData->postcode = '71200';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -1 day'))->setTime(22, 51, 55);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '14' => 1,
            ]
        );

        /** @var \App\Model\Customer\User\CustomerUser $customerUser */
        $customerUser = $this->customerUserRepository->findCustomerUserByEmailAndDomain(
            'no-reply.2@shopsys.com',
            $domainId
        );
        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Jan';
        $orderData->lastName = 'Nov??k';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420123456789';
        $orderData->street = 'Pouli??n?? 11';
        $orderData->city = 'M??stn??k';
        $orderData->postcode = '12345';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->companyName = 'shopsys s.r.o.';
        $orderData->companyNumber = '123456789';
        $orderData->companyTaxNumber = '987654321';
        $orderData->deliveryAddressSameAsBillingAddress = false;
        $orderData->deliveryFirstName = 'Karel';
        $orderData->deliveryLastName = 'Vesela';
        $orderData->deliveryCompanyName = 'Bestcompany';
        $orderData->deliveryTelephone = '+420987654321';
        $orderData->deliveryStreet = 'Zakopan?? 42';
        $orderData->deliveryCity = 'Zem??n';
        $orderData->deliveryPostcode = '54321';
        $orderData->deliveryCountry = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA);
        $orderData->note = 'Pros??m o dod??n?? do p??tku. D??kuji.';
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -4 day'))->setTime(21, 23, 5);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '1' => 2,
                ProductDataFixture::PRODUCT_PREFIX . '3' => 1,
            ],
            $customerUser
        );

        /** @var \App\Model\Customer\User\CustomerUser $customerUser */
        $customerUser = $this->customerUserRepository->findCustomerUserByEmailAndDomain(
            'no-reply.7@shopsys.com',
            $domainId
        );
        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_CZECH_POST);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'Jind??ich';
        $orderData->lastName = 'N??mec';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420123456789';
        $orderData->street = 'S??dli??tn?? 3259';
        $orderData->city = 'Orlov??';
        $orderData->postcode = '65421';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -4 day'))->setTime(11, 14, 2);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '2' => 2,
                ProductDataFixture::PRODUCT_PREFIX . '4' => 4,
            ],
            $customerUser
        );

        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
        $orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
        $orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_CANCELED);
        $orderData->firstName = 'Viktor';
        $orderData->lastName = 'P??tek';
        $orderData->email = 'no-reply@shopsys.com';
        $orderData->telephone = '+420888777111';
        $orderData->street = 'Vyhl??dkov?? 88';
        $orderData->city = 'Ostrava';
        $orderData->postcode = '71201';
        $orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC);
        $orderData->deliveryAddressSameAsBillingAddress = true;
        $orderData->domainId = $domainId;
        $orderData->currency = $domainDefaultCurrency;
        $orderData->createdAt = (new DateTime('now -7 day'))->setTime(11, 10, 47);
        $this->createOrder(
            $orderData,
            [
                ProductDataFixture::PRODUCT_PREFIX . '3' => 10,
            ]
        );
    }

    /**
     * @param \App\Model\Order\OrderData $orderData
     * @param array $products
     * @param \App\Model\Customer\User\CustomerUser $customerUser
     */
    private function createOrder(
        OrderData $orderData,
        array $products,
        ?CustomerUser $customerUser = null
    ) {
        $quantifiedProducts = [];
        foreach ($products as $productReferenceName => $quantity) {
            $product = $this->getReference($productReferenceName);
            $quantifiedProducts[] = new QuantifiedProduct($product, $quantity);
        }
        $orderPreview = $this->orderPreviewFactory->create(
            $orderData->currency,
            $orderData->domainId,
            $quantifiedProducts,
            $orderData->transport,
            $orderData->payment,
            $customerUser,
            null
        );

        /** @var \App\Model\Order\Order $order */
        $order = $this->orderFacade->createOrder($orderData, $orderPreview, $customerUser);
        $referenceName = self::ORDER_PREFIX . $order->getId();
        $this->addReference($referenceName, $order);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            ProductDataFixture::class,
            TransportDataFixture::class,
            PaymentDataFixture::class,
            CustomerUserDataFixture::class,
            OrderStatusDataFixture::class,
            CountryDataFixture::class,
            SettingValueDataFixture::class,
        ];
    }
}
