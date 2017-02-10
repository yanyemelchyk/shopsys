<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Customer;

use DateTime;
use Shopsys\ShopBundle\Component\String\HashGenerator;
use Shopsys\ShopBundle\Model\Customer\CustomerPasswordService;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Tests\Test\FunctionalTestCase;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class CustomerPasswordServiceTest extends FunctionalTestCase
{
    public function isResetPasswordHashValidProvider() {
        return [
            [
                'resetPasswordHash' => 'validHash',
                'resetPasswordHashValidThrough' => new DateTime('+1 hour'),
                'sentHash' => 'validHash',
                'isExpectedValid' => true,
            ],
            [
                'resetPasswordHash' => null,
                'resetPasswordHashValidThrough' => new DateTime('+1 hour'),
                'sentHash' => 'hash',
                'isExpectedValid' => false,
            ],
            [
                'resetPasswordHash' => 'validHash',
                'resetPasswordHashValidThrough' => new DateTime('+1 hour'),
                'sentHash' => 'invalidHash',
                'isExpectedValid' => false,
            ],
            [
                'resetPasswordHash' => 'validHash',
                'resetPasswordHashValidThrough' => null,
                'sentHash' => 'validHash',
                'isExpectedValid' => false,
            ],
            [
                'resetPasswordHash' => 'validHash',
                'resetPasswordHashValidThrough' => new DateTime('-1 hour'),
                'sentHash' => 'validHash',
                'isExpectedValid' => false,
            ],
        ];
    }

    /**
     * @dataProvider isResetPasswordHashValidProvider
     */
    public function testIsResetPasswordHashValid(
        $resetPasswordHash,
        $resetPasswordHashValidThrough,
        $sentHash,
        $isExpectedValid
    ) {
        $encoderFactory = $this->getContainer()->get('security.encoder_factory');
        $hashGenerator = $this->getContainer()->get(HashGenerator::class);

        $registrationService = new CustomerPasswordService(
            $encoderFactory,
            $hashGenerator
        );

        $userMock = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResetPasswordHash', 'getResetPasswordHashValidThrough'])
            ->getMock();

        $userMock->expects($this->any())->method('getResetPasswordHash')
            ->willReturn($resetPasswordHash);
        $userMock->expects($this->any())->method('getResetPasswordHashValidThrough')
            ->willReturn($resetPasswordHashValidThrough);

        $isResetPasswordHashValid = $registrationService->isResetPasswordHashValid($userMock, $sentHash);

        $this->assertSame($isExpectedValid, $isResetPasswordHashValid);
    }
}
