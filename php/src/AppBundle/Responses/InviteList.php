<?php

namespace AppBundle\Responses;

use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Annotation\Groups;

class InviteList extends AbstractResponse
{
    /**
     * @var array
     * @Type("array")
     * @Groups({"list","details"})
     */
    protected $data;

    /**
     * InviteList constructor.
     * @param array $invites
     * @param $basePath
     */
    public function __construct(array $invites, $basePath)
    {
        parent::__construct(200);

        $this->data = [
            'groupInvites' => [],
        ];

        if (!empty($invites)) {
            foreach ($invites as $invite) {
                $this->data['groupInvites'][] =
                    [
                        'group' => new GroupSummary($invite['groupUser'], $invite['userGoal'], false, $basePath)
                    ];
            }
        }
    }
}