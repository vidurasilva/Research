<?php

namespace AppBundle\Responses;

use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Annotation\Groups;

class CategoryList extends AbstractResponse
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
    public function __construct(array $entities, $basePath)
    {
        parent::__construct(200);

        $this->data = [
        	'categories' => []
        ];

        foreach ($entities as $entity) {
            $this->data['categories'][] = new CategorySummary($entity, $basePath, false);
        }
    }
}