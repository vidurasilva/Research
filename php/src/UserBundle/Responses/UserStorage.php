<?php

namespace UserBundle\Responses;

use ApiBundle\Responses\AbstractResponse;
use JMS\Serializer\Annotation\Type;

class UserStorage extends AbstractResponse
{
	/**
	 * @var string
	 * @Type("array")
	 */
	protected $data;

    /**
     * @param \UserBundle\Entity\User $entity
     * @param bool $inherit
     */
    public function __construct(\UserBundle\Entity\User $entity, $inherit = false)
    {
        if (!$inherit) {
            parent::__construct(200);
        }

        $this->data = [
        	'id' => $entity->getId(),
        	'notificationId' => $entity->getNotificationUserId(),
	        'email' => $entity->getEmail(),
		    'firstname' => $entity->getFirstname(),
		    'lastname' => $entity->getLastname(),
		    'nickname' => $entity->getNickName(),
		    'gender' => $entity->getGender(),
		    'telephone' => $entity->getTelephone(),
		    'roles' => $entity->getRoles(),
		    'enabled' => $entity->isEnabled(),
        ];
    }
}
