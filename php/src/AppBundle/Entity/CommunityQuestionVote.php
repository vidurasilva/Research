<?php

namespace AppBundle\Entity;

/**
 * CommunityQuestionVote
 */
class CommunityQuestionVote
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $vote;

	/**
	 * @var \UserBundle\Entity\User
	 */
	private $user;

	/**
	 * @var \AppBundle\Entity\CommunityQuestion
	 */
	private $questions;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set vote
     *
     * @param integer $vote
     *
     * @return CommunityQuestionVote
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return int
     */
    public function getVote()
    {
        return $this->vote;
    }

	/**
	 * @return \UserBundle\Entity\User
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param \UserBundle\Entity\User $user
	 *
	 * @return CommunityQuestionVote
	 */
	public function setUser($user)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * @return CommunityQuestion
	 */
	public function getQuestions()
	{
		return $this->questions;
	}

	/**
	 * @param CommunityQuestion $questions
	 *
	 * @return CommunityQuestionVote
	 */
	public function setQuestions($questions)
	{
		$this->questions = $questions;

		return $this;
	}
}
