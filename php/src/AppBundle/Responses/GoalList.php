<?php

namespace AppBundle\Responses;

use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Annotation\Groups;

class GoalList extends AbstractResponse
{
    /**
     * @var array
     * @Type("array")
     * @Groups({"list","details"})
     */
    protected $data;

    /**
     * PreferencesList constructor.
     * @param array $entities
     */
    public function __construct(array $entities, $uploadPath)
    {
        parent::__construct(200);

        $this->data['goals'] = [];

        foreach ($entities as $entity) {
            $this->data['goals'][] = new GoalSummary($entity, $uploadPath);
        }
    }
}