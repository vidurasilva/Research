<?php

namespace ApiBundle\Responses;

use ApiBundle\Responses\AbstractResponse;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

/**
 * Class AbstractResponse
 * @package ApiBundle\Responses
 */
class OffsetPaginatedResponse extends AbstractResponse
{
    /**
     * @var array
     */
    protected $pagination = [];

    public function __construct($statusCode, $offset, $limit, $total)
    {
        $this->pagination = ['offset' => $offset, 'limit' => $limit, 'total' => (int) $total];

        parent::__construct($statusCode);
    }
}