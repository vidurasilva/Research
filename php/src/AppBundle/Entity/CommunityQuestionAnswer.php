<?php

namespace AppBundle\Entity;

/**
 * CommunityQuestionAnswer
 */
class CommunityQuestionAnswer
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $answer;

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
     * Set answer
     *
     * @param string $answer
     *
     * @return CommunityQuestionAnswer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }
    /**
     * @var \AppBundle\Entity\CommunityQuestion
     */
    private $communityQuestion;


    /**
     * Set communityQuestion
     *
     * @param \AppBundle\Entity\CommunityQuestion $communityQuestion
     *
     * @return CommunityQuestionAnswer
     */
    public function setCommunityQuestion(\AppBundle\Entity\CommunityQuestion $communityQuestion = null)
    {
        $this->communityQuestion = $communityQuestion;

        return $this;
    }

    /**
     * Get communityQuestion
     *
     * @return \AppBundle\Entity\CommunityQuestion
     */
    public function getCommunityQuestion()
    {
        return $this->communityQuestion;
    }
    /**
     * @var \AppBundle\Entity\CommunityQuestion
     */
    private $questions;


    /**
     * Set questions
     *
     * @param \AppBundle\Entity\CommunityQuestion $questions
     *
     * @return CommunityQuestionAnswer
     */
    public function setQuestions(\AppBundle\Entity\CommunityQuestion $questions = null)
    {
        $this->questions = $questions;

        return $this;
    }

    /**
     * Get questions
     *
     * @return \AppBundle\Entity\CommunityQuestion
     */
    public function getQuestions()
    {
        return $this->questions;
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
     * @return CommunityQuestionAnswer
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
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;


    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return CommunityQuestionAnswer
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
     * @return CommunityQuestionAnswer
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $votes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->votes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add vote
     *
     * @param \AppBundle\Entity\CommunityQuestionAnswerVote $vote
     *
     * @return CommunityQuestionAnswer
     */
    public function addVote(\AppBundle\Entity\CommunityQuestionAnswerVote $vote)
    {
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param \AppBundle\Entity\CommunityQuestionAnswerVote $vote
     */
    public function removeVote(\AppBundle\Entity\CommunityQuestionAnswerVote $vote)
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
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \AppBundle\Entity\CommunityQuestionAnswer
     */
    private $parent;


    /**
     * Add child
     *
     * @param \AppBundle\Entity\CommunityQuestionAnswer $child
     *
     * @return CommunityQuestionAnswer
     */
    public function addChild(\AppBundle\Entity\CommunityQuestionAnswer $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\CommunityQuestionAnswer $child
     */
    public function removeChild(\AppBundle\Entity\CommunityQuestionAnswer $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\CommunityQuestionAnswer $parent
     *
     * @return CommunityQuestionAnswer
     */
    public function setParent(\AppBundle\Entity\CommunityQuestionAnswer $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\CommunityQuestionAnswer
     */
    public function getParent()
    {
        return $this->parent;
    }
}
