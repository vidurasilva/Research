<?php

namespace AppBundle\Responses;

use ApiBundle\Responses\OffsetPaginatedResponse;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use ApiBundle\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Request;

class CommunityQuestionList extends OffsetPaginatedResponse
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
    public function __construct(array $entities, $basePath, $offset, $limit, $total)
    {
        parent::__construct(200, $offset, $limit, $total);

        $this->data['questions'] = [];

        foreach ($entities as $entity) {
            $this->data['questions'][] = new CommunityQuestionSummary($entity, $basePath);
        }
    }
}