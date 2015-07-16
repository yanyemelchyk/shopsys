<?php

namespace SS6\ShopBundle\Model\Security\Exception;

use Exception;

class LoginFailedException extends Exception implements SecurityException {

	/**
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct($message, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
