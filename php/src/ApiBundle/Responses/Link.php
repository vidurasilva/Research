<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 28-6-16 11:47
 */

namespace ApiBundle\Responses;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

class Link
{
	/**
	 * @var string
	 * @Groups({"list","details"})
	 */
	private $route;

	/**
	 * @var array
	 * @Groups({"list","details"})
	 */
	private $params;

	/**
	 * Link constructor.
	 * @param array $params
	 * @param string $route
	 */
	public function __construct(array $params, $route)
	{
		$this->route = $route;
		$this->params = $params;
	}

	/**
	 * @return string
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}
}