<?php

namespace AppBundle\Responses;

use AppBundle\Entity\CharityCategory;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

class CharityCategorySummary
{
    /**
     * @var int
     * @Type("integer")
     * @Groups({"list","details"})
     */
    protected $id;

    /**
     * @var string
     * @Type("string")
     * @Groups({"list","details"})
     */
    protected $title;

	/**
	 * @var array
	 * @Type("array")
	 * @Groups({"list"})
	 */
	protected $charities;


    /**
     * UserSummary constructor.
     * @param CharityCategory $entity
     */
    public function __construct(CharityCategory $entity)
    {
        $this->id = $entity->getId();
        $this->title = $entity->getTitle();
	    $this->charities = [];
	    foreach ($entity->getCharities() as $charity) {
	    	$this->charities[] = new CharitySummary($charity);
	    }
    }
}