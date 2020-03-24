<?php

namespace AppBundle\Responses;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use AppBundle\Entity\CommunityCategory;
use Symfony\Component\HttpFoundation\Request;

class CommunityCategorySummary
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
     * @var string
     * @Type("string")
     * @Groups({"list","details"})
     */
    protected $icon;

    /**
     * @var integer
     * @Type("integer")
     * @Groups({"list","details"})
     */
    protected $totalQuestions = 0;

    /**
     * @var integer
     * @Type("integer")
     * @Groups({"list","details"})
     */
    protected $children = 0;

    /**
     * CommunityCategorySummary constructor.
     * @param CommunityCategory $entity
     * @param $baseurl
     */
    public function __construct(CommunityCategory $entity, $baseurl, $totalQuestions = 0)
    {
        $this->id = $entity->getId();
        $this->title = $entity->getTitle();
        $this->icon = $entity->getIcon() ? sprintf('%s%s', $baseurl, $entity->getIcon()) : sprintf('%s%s', $baseurl, '/data/uploads/alarm-clock.png');

        if (!empty($entity->getChildren())) {
            $this->children = count($entity->getChildren());
        }

        if (!empty($entity->getQuestions())) {
            $this->totalQuestions = $entity->getQuestions()->count();
	        foreach ($entity->getChildren() as $child) {
	        	$this->totalQuestions += $this->countQuestions($child);
	        }
        }
    }

    public function countQuestions(CommunityCategory $category)
    {
		$count = $category->getQuestions()->count();
	    foreach ($category->getChildren() as $child) {
		    $count += $this->countQuestions($child);
	    }

	    return $count;
    }
}