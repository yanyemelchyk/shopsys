<?php

namespace SS6\ShopBundle\Model\Payment;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Table(name="payment_translations")
 * @ORM\Entity
 */
class PaymentTranslation extends AbstractTranslation {

	/**
	 * @Prezent\Translatable(targetEntity="SS6\ShopBundle\Model\Payment\Payment")
	 */
	protected $translatable;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

}