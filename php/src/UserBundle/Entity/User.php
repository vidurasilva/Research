<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Entity\User as BaseUser;


class User extends BaseUser
{

    const ROLE_ADMIN = 'ROLE_ADMIN';

    const ROLE_AUTHENTICATED = 'IS_AUTHENTICATED_FULLY';

    const ROLE_USER = 'ROLE_USER';

    const MIN_LENGTH_PASSWORD = 6;

    public static function user_roles()
    {
        return [
          'ROLE_ADMIN' => self::ROLE_ADMIN,
          'IS_AUTHENTICATED_FULLY' => self::ROLE_AUTHENTICATED,
          'ROLE_USER' => self::ROLE_USER,
        ];
    }

    public function __construct()
    {
        parent::__construct();
        $this->paymentCharges = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     *
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $firstname;

    /**
     * @var string
     */
    protected $lastname;

    /**
     * @var string
     */
    protected $nickName;

    /**
     * @var string
     */
    protected $gender;

    /**
     * @var integer
     */
    protected $facebookUserId;

    /**
     * @var string
     */
    protected $telephone;

    /**
     * @var \DateTime
     */
    protected $lastActivity;

    /**
     * @var string
     */
    protected $notificationUserId;

    /**
     * LifeCycleEvent.
     */
    public function prePersist()
    {
        if (null === $this->nickName) {
            $this->setNickName($this->generateNickName());
        }
    }

    /**
     * @return string
     */
    protected function generateNickName()
    {
        return sprintf('%s%s', strtolower($this->firstname),
          strtolower($this->lastname));
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }


    /**
     * @return mixed
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     * @param mixed $nickName
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return int
     */
    public function getFacebookUserId()
    {
        return $this->facebookUserId;
    }

    /**
     * @param int $facebookUserId
     */
    public function setFacebookUserId($facebookUserId)
    {
        $this->facebookUserId = $facebookUserId;
    }

    /**
     * @return string
     */
    public function getNotificationUserId()
    {
        return $this->notificationUserId;
    }

    /**
     * @param string $notificationUserId
     */
    public function setNotificationUserId($notificationUserId)
    {
        $this->notificationUserId = $notificationUserId;
    }

    /**
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     *
     * @return $this
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return \DateTime
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * @param \DateTime $lastActivity
     *
     * @return $this
     */
    public function setLastActivity(\DateTime $lastActivity)
    {
        $this->lastActivity = $lastActivity;
        return $this;
    }

    /**
     * @var string
     */
    private $googleUserId;


    /**
     * Set googleUserId
     *
     * @param string $googleUserId
     *
     * @return User
     */
    public function setGoogleUserId($googleUserId)
    {
        $this->googleUserId = $googleUserId;

        return $this;
    }

    /**
     * Get googleUserId
     *
     * @return string
     */
    public function getGoogleUserId()
    {
        return $this->googleUserId;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userGoals;


    /**
     * Add userGoal
     *
     * @param \AppBundle\Entity\UserGoal $userGoal
     *
     * @return User
     */
    public function addUserGoal(\AppBundle\Entity\UserGoal $userGoal)
    {
        $this->userGoals[] = $userGoal;

        return $this;
    }

    /**
     * Remove userGoal
     *
     * @param \AppBundle\Entity\UserGoal $userGoal
     */
    public function removeUserGoal(\AppBundle\Entity\UserGoal $userGoal)
    {
        $this->userGoals->removeElement($userGoal);
    }

    /**
     * Get userGoals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserGoals()
    {
        return $this->userGoals;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $questions;


    /**
     * Add question
     *
     * @param \AppBundle\Entity\CommunityQuestion $question
     *
     * @return User
     */
    public function addQuestion(\AppBundle\Entity\CommunityQuestion $question)
    {
        $this->questions[] = $question;

        return $this;
    }

    /**
     * Remove question
     *
     * @param \AppBundle\Entity\CommunityQuestion $question
     */
    public function removeQuestion(\AppBundle\Entity\CommunityQuestion $question
    ) {
        $this->questions->removeElement($question);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $group;


    /**
     * Get group
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @var string
     */
    private $paymentUser;


    /**
     * Set paymentUser
     *
     * @param string $paymentUser
     *
     * @return User
     */
    public function setPaymentUser($paymentUser)
    {
        $this->paymentUser = $paymentUser;

        return $this;
    }

    /**
     * Get paymentUser
     *
     * @return string
     */
    public function getPaymentUser()
    {
        return $this->paymentUser;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $paymentCharges;


    /**
     * Add paymentCharge
     *
     * @param \AppBundle\Entity\PaymentCharge $paymentCharge
     *
     * @return User
     */
    public function addPaymentCharge(
      \AppBundle\Entity\PaymentCharge $paymentCharge
    ) {
        $this->paymentCharges[] = $paymentCharge;

        return $this;
    }

    /**
     * Remove paymentCharge
     *
     * @param \AppBundle\Entity\PaymentCharge $paymentCharge
     */
    public function removePaymentCharge(
      \AppBundle\Entity\PaymentCharge $paymentCharge
    ) {
        $this->paymentCharges->removeElement($paymentCharge);
    }

    /**
     * Get paymentCharges
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentCharges()
    {
        return $this->paymentCharges;
    }

    /**
     * @var string
     */
    private $profilePicture;


    /**
     * Set profilePicture
     *
     * @param string $profilePicture
     *
     * @return User
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    /**
     * Get profilePicture
     *
     * @return string
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }
}
