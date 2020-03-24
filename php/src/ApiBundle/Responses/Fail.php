<?php

namespace ApiBundle\Responses;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

class Fail
{
	/**
	 * @Type("string")
	 * @Groups({"list","details"})
	 * @var string
	 */
	protected $message;

	/**
	 * @type("integer")
	 * @Groups({"list","details"})
	 * @var integer
	 */
	protected $code;

	/**
	 * @param string $message
	 * @param integer $code
	 */
	public function __construct($message, $code)
	{
		$this->message = $message;
		$this->code = $code;
	}
}