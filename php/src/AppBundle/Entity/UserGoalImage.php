<?php

namespace AppBundle\Entity;

/**
 * UserGoalImage
 */
class UserGoalImage
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $image;

    /**
     * @var string
     */
    private $originalName;

    /**
     * @var \AppBundle\Entity\UserGoal
     */
    private $userGoal;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return UserGoalImage
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set originalName
     *
     * @param string $originalName
     *
     * @return UserGoalImage
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Get originalName
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Set userGoal
     *
     * @param \AppBundle\Entity\UserGoal $userGoal
     *
     * @return UserGoalImage
     */
    public function setUserGoal(\AppBundle\Entity\UserGoal $userGoal = null)
    {
        $this->userGoal = $userGoal;

        return $this;
    }

    /**
     * Get userGoal
     *
     * @return \AppBundle\Entity\UserGoal
     */
    public function getUserGoal()
    {
        return $this->userGoal;
    }
    /**
     * @var boolean
     */
    private $mailSended = 0;

    /**
     * @var integer
     */
    private $mailAttemps = 0;


    /**
     * Set mailSended
     *
     * @param boolean $mailSended
     *
     * @return UserGoalImage
     */
    public function setMailSended($mailSended)
    {
        $this->mailSended = $mailSended;

        return $this;
    }

    /**
     * Get mailSended
     *
     * @return boolean
     */
    public function getMailSended()
    {
        return $this->mailSended;
    }

    /**
     * Set mailAttemps
     *
     * @param integer $mailAttemps
     *
     * @return UserGoalImage
     */
    public function setMailAttemps($mailAttemps)
    {
        $this->mailAttemps = $mailAttemps;

        return $this;
    }

    /**
     * Get mailAttemps
     *
     * @return integer
     */
    public function getMailAttemps()
    {
        return $this->mailAttemps;
    }
}
