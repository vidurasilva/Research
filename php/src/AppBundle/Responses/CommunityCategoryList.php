<?php

namespace AppBundle\Responses;

use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Annotation\Groups;

class CommunityCategoryList extends AbstractResponse
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
    public function __construct(array $entities, $basePath, $totalQuestions)
    {
        parent::__construct(200);

        $this->data = [];

        foreach ($entities as $entity) {
            $this->data['categories'][] = new CommunityCategorySummary($entity, $basePath, $totalQuestions);
        }
    }
}