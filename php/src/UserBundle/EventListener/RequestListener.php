<?php
/**
 * @author smaessen@e-sites.nl
 * @since 18-7-16 14:19
 */

namespace UserBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use UserBundle\Service\UserService;

class RequestListener
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * RequestListener constructor.
     * @param TokenStorage $tokenStorage
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return void
     */
    public function onKernelRequest()
    {
        $this->userService->updateLastActivity();
    }
}