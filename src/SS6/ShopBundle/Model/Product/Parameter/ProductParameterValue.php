<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Product\Parameter\Parameter;
use SS6\ShopBundle\Model\Product\Parameter\ParameterValue;
use SS6\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(name="product_parameter_values")
 * @ORM\Entity
 */
class ProductParameterValue {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 * @ORM\JoinColumn(nullable=false, name="product_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $product;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Parameter\Parameter")
	 * @ORM\JoinColumn(nullable=false, name="parameter_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $parameter;

	/**
	 * @var string
	 *
	 * @ORM\Id
	 * @ORM\Column(name="locale", type="string")
	 */
	private $locale;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterValue
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Parameter\ParameterValue")
	 * @ORM\JoinColumn(name="value_id", referencedColumnName="id", nullable=false)
	 */
	private $value;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\Parameter\Parameter $parameter
	 * @param string $locale
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterValue $value
	 */
	public function __construct(
		Product $product,
		Parameter $parameter,
		$locale,
		ParameterValue $value
	) {
		$this->product = $product;
		$this->parameter = $parameter;
		$this->locale = $locale;
		$this->value = $value;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	public function getParameter() {
		return $this->parameter;
	}

	/**
	 * @return string
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ParameterValue
	 */
	public function getValue() {
		return $this->value;
	}

}
