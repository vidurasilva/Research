<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 28-6-16 14:33
 */

namespace ApiBundle\Responses;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

class LinkSet
{
	/**
	 * @Type("ApiBundle\Responses\Link")
	 * @Groups({"list","details"})
	 * @var Link
	 */
	protected $_self;

	public function __construct(array $links)
	{
		foreach ($links as $name => $object) {
			$this->$name = $object;
		}
	}
}