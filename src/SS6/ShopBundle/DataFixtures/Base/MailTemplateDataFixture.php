<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MailTemplateData;

class MailTemplateDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('Děkujeme za objednávku');
		$mailTemplateData->setBody('Dobrý den,<br /><br />'
			. 'Vaše objednávka byla úspěšně vytvořena.<br /><br />'
			. 'O dalších stavech objednávky Vás budeme informovat.');

		$mailTemplate = new MailTemplate('order_status_1', $mailTemplateData);
		$manager->persist($mailTemplate);
		$manager->flush();
	}

}
