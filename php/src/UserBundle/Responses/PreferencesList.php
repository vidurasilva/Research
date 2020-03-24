<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 28-6-16 11:15
 */

namespace UserBundle\Responses;

use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use UserBundle\Entity\User as UserEntity;

class PreferencesList extends AbstractResponse
{
	/**
	 * @var array
	 * @Type("array<UserBundle\Responses\TeamPreference>")
	 */
	protected $data;

	/**
	 * PreferencesList constructor.
	 * @param UserEntity $entity
	 */
	public function __construct(UserEntity $entity)
	{
		parent::__construct(200);

		$this->data = [];

		foreach ($entity->getTeamPreferences() as $preference) {
			$this->data[] = new TeamPreference($preference);
		}
	}
}