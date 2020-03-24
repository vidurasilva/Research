<?php

namespace AppBundle\Responses;

use AppBundle\Entity\GoalCategory;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Annotation\Groups;

class CategorySummary
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
     * @type("string")
     * @Groups({"list","details"})
     */
    protected $icon;

    /**
     * UserSummary constructor.
     * @param Category $entity
     */
    public function __construct(GoalCategory $entity, $baseurl)
    {
        $this->id = $entity->getId();
        $this->title = $entity->getTitle();
        //$this->icon = sprintf('%s%s', $request->getSchemeAndHttpHost(), '/data/uploads/alarm-clock.png'); //@todo: fetch from db
	    $this->icon = $entity->getIcon() ? sprintf('%s%s', $baseurl, $entity->getIcon()) : sprintf('%s%s', $baseurl, '/data/uploads/alarm-clock.png');
    }
}