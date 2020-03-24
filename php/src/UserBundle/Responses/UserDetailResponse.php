<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 12-7-16 13:01
 */

namespace UserBundle\Responses;

use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use UserBundle\Entity\User as UserEntity;

class UserDetailResponse extends AbstractResponse
{
	/**
	 * @var UserSummary
	 * @Type("UserBundle\Responses\UserSummary")
	 */
	protected $data;

	/**
	 * UserDetailResponse constructor.
	 */
	public function __construct(UserEntity $entity, $basePath = null)
	{
		parent::__construct(200);

		$this->data = new UserSummary($entity, $basePath);
	}


}