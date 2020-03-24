<?php

namespace ApiBundle\Exception;

use ApiBundle\Exception\ApiException;
use ApiBundle\Exception\FailRegistry;

class MissingParametersException extends ApiException
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * MissingParametersException constructor.
     * @param string|array $parameters
     */
    public function __construct($parameters = [])
    {
        $this->parameters = $parameters;

        if (is_string($parameters)) {
            $this->parameters = [$parameters];
        }

        return parent::__construct(
            sprintf(FailRegistry::getByCode(FailRegistry::GENERAL_MISSING_PARAMETERS), implode(', ', $this->parameters)),
            FailRegistry::GENERAL_MISSING_PARAMETERS
        );
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}