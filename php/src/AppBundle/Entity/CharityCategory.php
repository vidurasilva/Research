<?php

namespace AppBundle\Entity;

/**
 * CharityCategory
 */
class CharityCategory
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $charities;

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->charities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return CharityCategory
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
     * Add charity
     *
     * @param \AppBundle\Entity\Charity $charity
     *
     * @return CharityCategory
     */
    public function addCharity(\AppBundle\Entity\Charity $charity)
    {
        $this->charities[] = $charity;

        return $this;
    }

    /**
     * Remove charity
     *
     * @param \AppBundle\Entity\Charity $charity
     */
    public function removeCharity(\AppBundle\Entity\Charity $charity)
    {
        $this->charities->removeElement($charity);
    }

    /**
     * Get charities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharities()
    {
        return $this->charities;
    }
}
