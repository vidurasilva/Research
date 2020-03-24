<?php

namespace AppBundle\Responses;

use ApiBundle\Responses\AbstractResponse;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use UserBundle\Responses\UserSummary;
use AppBundle\Entity\CommunityQuestion;
use Symfony\Component\HttpFoundation\Request;

class CommunityQuestionDetail extends AbstractResponse
{
    /**
     * @var array
     * @Type("array")
     * @Groups({"list","details"})
     */
    protected $data;

    /**
     * CommunityQuestionDetail constructor.
     * @param CommunityQuestion $question
     * @param $basePath
     */
    public function __construct(CommunityQuestion $question, $basePath)
    {
        parent::__construct(200);
        $this->data['question'] = [
            'id' => $question->getId(),
            'title' => $question->getTitle(),
            'created' => $question->getCreated(),
            'answers_count' => !empty($question->getAnswers()) ? count($question->getAnswers()) : 0
        ];

        if ($question->getUser()) {
            $this->data['question']['user'] = new UserSummary($question->getUser(), $basePath);
        }
    }
}