<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 28-6-16 11:15
 */

namespace UserBundle\Responses;

use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use UserBundle\Entity\User as UserEntity;

class UserList extends AbstractResponse
{
	/**
	 * @var array
	 * @Type("array<UserBundle\Responses\UserSummary>")
	 */
	protected $data;

	/**
	 * PreferencesList constructor.
	 * @param array $entities
	 */
	public function __construct(array $entities)
	{
		parent::__construct(200);

		$this->data = [];

		foreach ($entities as $entity) {
			$this->data[] = new UserSummary($entity);
		}
	}
}