<?php

namespace UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use UserBundle\Entity\User;

class UserEvent extends Event
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param User $user
     * @param array $data
     */
    public function __construct(User $user, array $data = [])
    {
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}