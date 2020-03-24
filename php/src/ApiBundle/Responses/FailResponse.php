<?php

namespace ApiBundle\Responses;

use ApiBundle\Exception\FailRegistry;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

class FailResponse extends AbstractResponse
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
	public function __construct($code, $parameters = [], $statusCode = 400)
	{
		parent::__construct($statusCode);

		$this->data = new Fail(
			vsprintf(FailRegistry::getByCode($code), $parameters),
			$code
		);
	}
}