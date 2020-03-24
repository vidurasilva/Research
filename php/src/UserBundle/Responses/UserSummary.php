<?php

namespace UserBundle\Responses;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use ApiBundle\Responses\AbstractResponse;

/**
 * Class UserSummary
 * @package UserBundle\Responses
 */
class UserSummary extends AbstractResponse
{
    // added so it will not be added to the response
    protected $status;
    /**
     * @var int
     * @Type("integer")
     * @Groups({"list","details"})
     */
    protected $id;

    /**
     * @var string
     * @Type("string")
     * @Groups({"list","details"})
     */
    protected $email;

    /**
     * @var string
     * @Type("string")
     * @Groups({"list","details"})
     */
    protected $firstname;

    /**
     * @var string
     * @Type("string")
     * @Groups({"list","details"})
     */
    protected $lastname;

    /**
     * @var string
     * @Type("string")
     * @Groups({"list","details"})
     */
    protected $nickname;

    /**
     * @var string
     * @Type("string")
     * @Groups({"list","details"})
     */
    protected $gender;

    /**
     * @var string
     * @Type("string")
     * @Groups({"list","details"})
     */
    protected $telephone;

    /**
     * @var array
     * @Type("array")
     * @Groups({"list","details"})
     */
    protected $roles;

    /**
     * @var boolean
     * @Type("boolean")
     * @Groups({"list","details"})
     */
    protected $isEnabled;

    /**
     * @var string
     * @Type("string")
     * @Groups({"list","details"})
     */
    protected $lastActivity;

    /**
     * @var string
     * @Type("string")
     * @Groups({"list","details"})
     */
    protected $avatar;

    /**
     * @param \UserBundle\Entity\User $entity
     */
    public function __construct(\UserBundle\Entity\User $entity, $basePath)
    {
        $this->id = $entity->getId();
        $this->email = $entity->getEmail();
        $this->firstname = $entity->getFirstname();
        $this->lastname = $entity->getLastname();
        $this->nickname = $entity->getNickName();
        $this->gender = $entity->getGender();
        $this->roles = $entity->getRoles();
        $this->telephone = $entity->getTelephone();
        $this->isEnabled = $entity->isEnabled();
        $this->lastActivity = $entity->getLastActivity() ? $entity->getLastActivity()->format('Y-m-d H:i:s') : null;

	    if ($entity->getProfilePicture()) {
	    	$this->avatar = $basePath . 'profile-pictures/' . $entity->getProfilePicture();
	    } else {
		    $basePath = str_replace('/data/uploads/', '/assets/images/', $basePath);
		    $this->avatar = $basePath.'dummy-avatar.jpg'; //@todo:Change static avatar to dynamic user avatar.
	    }
    }
}