<?php

namespace AppBundle\Entity;

/**
 * Charity
 */
class Charity
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
     * @var \AppBundle\Entity\CharityCategory
     */
    private $charityCategory;

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
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
     * @return Charity
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
     * Set charityCategory
     *
     * @param \AppBundle\Entity\CharityCategory $charityCategory
     *
     * @return Charity
     */
    public function setCharityCategory(\AppBundle\Entity\CharityCategory $charityCategory = null)
    {
        $this->charityCategory = $charityCategory;

        return $this;
    }

    /**
     * Get charityCategory
     *
     * @return \AppBundle\Entity\CharityCategory
     */
    public function getCharityCategory()
    {
        return $this->charityCategory;
    }

}
