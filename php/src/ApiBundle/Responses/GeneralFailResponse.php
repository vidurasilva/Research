<?php

namespace ApiBundle\Responses;

use ApiBundle\Exception\FailRegistry;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

class GeneralFailResponse extends AbstractResponse
{
	/**
	 * Success message
	 *
	 * @Type("ApiBundle\Responses\Fail")
	 * @Groups({"list","details"})
	 * @var Fail
	 */
	protected $data;

	/**
	 * 'fail'
	 *
	 * @type("string")
	 * @Groups({"list","details"})
	 * @var string
	 */
	protected $status;

	/**
	 * @param int $code
	 * @param array $parameters
	 * @param int $statusCode
	 * @throws \Exception
	 */
	public function __construct($code, $message, $statusCode = 400)
	{
		parent::__construct($statusCode);

		$this->data = new Fail(
			$message,
			$code
		);
	}
}