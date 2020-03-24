<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 29-6-16 10:54
 */

namespace ApiBundle\Responses;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

class SuccessResponse extends AbstractResponse
{
	/**
	 * Success message
	 *
	 * @Type("string")
	 * @Groups({"list","details"})
	 * @var string
	 */
	protected $data;

	/**
	 * 'success'
	 *
	 * @type("string")
	 * @Groups({"list","details"})
	 * @var string
	 */
	protected $status;

	/**
	 * @param mixed $message
	 */
	public function __construct($message)
	{
		parent::__construct(200);
		$this->data = $message;
	}
}