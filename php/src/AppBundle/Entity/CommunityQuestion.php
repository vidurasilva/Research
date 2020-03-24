<?php

namespace AppBundle\Entity;

use AppBundle\Entity\CommunityQuestionVote;
use DMS\Filter\Rules as Filter;
use AppBundle\Entity\Traits\CrudTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Timestampable\Traits\Timestampable;


/**
 * CommunityQuestion
 */
class CommunityQuestion
{
    use Timestampable;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     * @Filter\StripTags()
     * @Filter\Trim()
     * @Filter\StripNewlines()
     */
    protected $title;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var \AppBundle\Entity\CommunityCategory
     */
    private $communityCategory;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $votes;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return CommunityQuestion
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
     * @return CommunityQuestion
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
     * Set communityCategory
     *
     * @param \AppBundle\Entity\CommunityCategory $communityCategory
     *
     * @return CommunityQuestion
     */
    public function setCommunityCategory(\AppBundle\Entity\CommunityCategory $communityCategory = null)
    {
        $this->communityCategory = $communityCategory;

        return $this;
    }

    /**
     * Get communityCategory
     *
     * @return \AppBundle\Entity\CommunityCategory
     */
    public function getCommunityCategory()
    {
        return $this->communityCategory;
    }
    /**
     * @var \UserBundle\Entity\User
     */
    private $user;


    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return CommunityQuestion
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * @var \AppBundle\Entity\CommunityQuestionAnswer
     */
    private $answers;


    /**
     * Set answers
     *
     * @param \AppBundle\Entity\CommunityQuestionAnswer $answers
     *
     * @return CommunityQuestion
     */
    public function setAnswers(\AppBundle\Entity\CommunityQuestionAnswer $answers = null)
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * Get answers
     *
     * @return \AppBundle\Entity\CommunityQuestionAnswer
     */
    public function getAnswers()
    {
        return $this->answers;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->answers = new ArrayCollection();
	    $this->votes = new ArrayCollection();
    }

    /**
     * Add answer
     *
     * @param \AppBundle\Entity\CommunityQuestionAnswer $answer
     *
     * @return CommunityQuestion
     */
    public function addAnswer(\AppBundle\Entity\CommunityQuestionAnswer $answer)
    {
        $this->answers[] = $answer;

        return $this;
    }

    /**
     * Remove answer
     *
     * @param \AppBundle\Entity\CommunityQuestionAnswer $answer
     */
    public function removeAnswer(\AppBundle\Entity\CommunityQuestionAnswer $answer)
    {
        $this->answers->removeElement($answer);
    }

	/**
	 * Add vote
	 *
	 * @param CommunityQuestionVote $vote
	 *
	 * @return CommunityQuestion
	 */
	public function addVote(CommunityQuestionVote $vote)
	{
		$this->votes[] = $vote;

		return $this;
	}

	/**
	 * Remove vote
	 *
	 * @param CommunityQuestionVote $vote
	 */
	public function removeVote(CommunityQuestionVote $vote)
	{
		$this->votes->removeElement($vote);
	}

	/**
	 * Get votes
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getVotes()
	{
		return $this->votes;
	}
}
