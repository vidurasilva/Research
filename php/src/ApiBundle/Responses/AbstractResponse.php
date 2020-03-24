<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 28-6-16 11:27
 */

namespace ApiBundle\Responses;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

/**
 * Class AbstractResponse
 * @package ApiBundle\Responses
 */
abstract class AbstractResponse
{
	/**
	 * Can be 'success', 'error' or 'fail'.
	 *
	 * @type("string")
	 * @Groups({"list","details"})
	 * @var string
	 */
	protected $status;

	/**
	 * @var int
	 * @Serializer\Exclude
	 */
	private $statusCode;

    /**
     * @var int
     * @Serializer\Exclude
     */
    private $pagination;
	/**
	 * @param integer $statusCode
	 */
	public function __construct($statusCode)
	{
		$statusCodeStart = floor($statusCode / 100);
		switch ($statusCodeStart) {
			case 1:
			case 2:
			case 3:
				$this->status = 'success';
				break;
			case 4:
				$this->status = 'fail';
				break;
			case 5:
				$this->status = 'error';
				break;
			default:
				throw new \InvalidArgumentException("Status code must be between 100 and 599");
		}
		$this->statusCode = $statusCode;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return int
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}
}