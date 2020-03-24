<?php

namespace AppBundle\Entity;

/**
 * GroupInvite
 */
class GroupInvite
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var string
     */
    private $email;

    /**
     * @var boolean
     */
    private $enrolled = 0;

    /**
     * @var \AppBundle\Entity\GoalGroup
     */
    private $group;


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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return GroupInvite
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return GroupInvite
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return GroupInvite
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set enrolled
     *
     * @param boolean $enrolled
     *
     * @return GroupInvite
     */
    public function setEnrolled($enrolled)
    {
        $this->enrolled = $enrolled;

        return $this;
    }

    /**
     * Get enrolled
     *
     * @return boolean
     */
    public function getEnrolled()
    {
        return $this->enrolled;
    }

    /**
     * Set group
     *
     * @param \AppBundle\Entity\GoalGroup $group
     *
     * @return GroupInvite
     */
    public function setGroup(\AppBundle\Entity\GoalGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \AppBundle\Entity\GoalGroup
     */
    public function getGroup()
    {
        return $this->group;
    }
    /**
     * @var boolean
     */
    private $mail_sended = 0;

    /**
     * @var integer
     */
    private $mail_attemps;


    /**
     * Set mailSended
     *
     * @param boolean $mailSended
     *
     * @return GroupInvite
     */
    public function setMailSended($mailSended)
    {
        $this->mail_sended = $mailSended;

        return $this;
    }

    /**
     * Get mailSended
     *
     * @return boolean
     */
    public function getMailSended()
    {
        return $this->mail_sended;
    }

    /**
     * Set mailAttemps
     *
     * @param integer $mailAttemps
     *
     * @return GroupInvite
     */
    public function setMailAttemps($mailAttemps)
    {
        $this->mail_attemps = $mailAttemps;

        return $this;
    }

    /**
     * Get mailAttemps
     *
     * @return integer
     */
    public function getMailAttemps()
    {
        return $this->mail_attemps;
    }
}
