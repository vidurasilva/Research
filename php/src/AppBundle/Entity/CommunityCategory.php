<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CrudTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CommunityCategory
 */
class CommunityCategory
{
    use CrudTrait;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var ArrayCollection
     */
    protected $parent;

    /**
     * @var ArrayCollection
     */
    protected $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $questions;


    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\CommunityCategory $child
     *
     * @return CommunityCategory
     */
    public function addChild(\AppBundle\Entity\CommunityCategory $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\CommunityCategory $child
     */
    public function removeChild(\AppBundle\Entity\CommunityCategory $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Add question
     *
     * @param \AppBundle\Entity\CommunityQuestion $question
     *
     * @return CommunityCategory
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
    public function removeQuestion(\AppBundle\Entity\CommunityQuestion $question)
    {
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
    private $goals;


    /**
     * Add goal
     *
     * @param \AppBundle\Entity\Goal $goal
     *
     * @return CommunityCategory
     */
    public function addGoal(\AppBundle\Entity\Goal $goal)
    {
        $this->goals[] = $goal;

        return $this;
    }

    /**
     * Remove goal
     *
     * @param \AppBundle\Entity\Goal $goal
     */
    public function removeGoal(\AppBundle\Entity\Goal $goal)
    {
        $this->goals->removeElement($goal);
    }

    /**
     * Get goals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGoals()
    {
        return $this->goals;
    }
}
