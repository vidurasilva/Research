<?php

namespace AppBundle\Responses;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use UserBundle\Responses\UserSummary;
use AppBundle\Entity\CommunityQuestion;
use ApiBundle\Responses\AbstractResponse;
use AppBundle\Entity\CommunityQuestionAnswer;
use JMS\Serializer\Annotation as Serializer;

class CommunityAnswerSummary extends AbstractResponse
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
     */
    protected $answer;

    /**
     * @var \DateTime
     * @Type("DateTime")
     */
    protected $created;

    /**
     * @var UserSummary
     * @Type("UserBundle\Responses\UserSummary")
     * @Groups({"list","details"})
     */
    protected $user;

    /**
     * @var CommunityAnswerList
     * @Type("array")
     * @Groups({"list","details"})
     */
    protected $comments;

    /**
     * CommunityAnswerSummary constructor.
     * @param CommunityQuestionAnswer $entity
     * @param $basePath
     */
    public function __construct(CommunityQuestionAnswer $entity, $basePath)
    {
    	parent::__construct(200);
        $this->id      = $entity->getId();
        $this->answer  = $entity->getAnswer();
        $this->created = $entity->getCreated();
        $this->user    = $entity->getUser() ? new UserSummary($entity->getUser(), $basePath) : NULL;

        if ($entity->getChildren()) {
            foreach ($entity->getChildren() as $child) {
                $this->comments[] = new CommunityAnswerSummary($child, $basePath);
            }
        }
    }
}