<?php

namespace AppBundle\Responses;

use JMS\Serializer\Annotation\Type;
use UserBundle\Responses\UserSummary;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Expose;
use AppBundle\Entity\CommunityQuestion;
use ApiBundle\Responses\AbstractResponse;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Annotation\ExclusionPolicy;

class CommunityQuestionSummary extends AbstractResponse
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
    protected $title;

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
     * @var integer
     * @Type("integer")
     * @Groups({"list","details"})
     */
    protected $answers_count = 0;

    /**
     * @var
     * @Type("AppBundle\Responses\CommunityCategorySummary")
     * @Groups({"list","details"})
     */
    protected $category;

    /**
     * CommunityQuestionSummary constructor.
     * @param CommunityQuestion $entity
     */
    public function __construct(CommunityQuestion $entity, $basePath)
    {
	    parent::__construct(200);

        $this->id = $entity->getId();
        $this->title = $entity->getTitle();
        $this->created = $entity->getUpdated();
        $this->category = new CommunityCategorySummary($entity->getCommunityCategory(), $basePath);
        $this->user = $entity->getUser() ? new UserSummary($entity->getUser(), $basePath) : null;
        $this->answers_count = !empty($entity->getAnswers()) ? count($entity->getAnswers()) : 0;
    }
}