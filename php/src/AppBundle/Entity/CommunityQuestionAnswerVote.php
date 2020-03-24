<?php

namespace AppBundle\Entity;

/**
 * CommunityQuestionAnswerVote
 */
class CommunityQuestionAnswerVote
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
     * @return CommunityQuestionAnswerVote
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
     * @var \AppBundle\Entity\CommunityCategoryAnswer
     */
    private $communityCategoryAnswer;

    /**
     * @var \UserBundle\Entity\User
     */
    private $user;


    /**
     * Set communityCategoryAnswer
     *
     * @param \AppBundle\Entity\CommunityCategoryAnswer $communityCategoryAnswer
     *
     * @return CommunityQuestionAnswerVote
     */
    public function setCommunityCategoryAnswer($communityCategoryAnswer = null)
    {
        $this->communityCategoryAnswer = $communityCategoryAnswer;

        return $this;
    }

    /**
     * Get communityCategoryAnswer
     *
     * @return \AppBundle\Entity\CommunityCategoryAnswer
     */
    public function getCommunityCategoryAnswer()
    {
        return $this->communityCategoryAnswer;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return CommunityQuestionAnswerVote
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
     * @return CommunityQuestionAnswerVote
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
}
