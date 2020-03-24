<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CrudTrait;

/**
 * GoalCategory
 */
class GoalCategory
{
	use CrudTrait;


    /**
     * @var string
     */
	protected $icon;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
	protected $goals;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->goals = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set title
     *
     * @param string $title
     *
     * @return GoalCategory
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return GoalCategory
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Add goal
     *
     * @param \AppBundle\Entity\Goal $goal
     *
     * @return GoalCategory
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
