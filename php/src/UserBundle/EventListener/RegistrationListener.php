<?php

namespace UserBundle\EventListener;

use AppBundle\Entity\GroupInvite;
use AppBundle\Entity\GroupUser;
use AppBundle\Service\MailerService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use UserBundle\Entity\User;
use UserBundle\Event\UserEvent;
use UserBundle\Service\UserService;

class RegistrationListener implements EventSubscriberInterface
{
    const EVENT_POSTREGISTRATION = 'user.registration.post';

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var MailerService
     */
	protected $mailerService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

	/**
     * @param UserService $userService
     * @param MailerService $mailerService
     */
    public function __construct(UserService $userService, MailerService $mailerService, EntityManager $entityManager)
    {
        $this->userService = $userService;
        $this->mailerService = $mailerService;
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            self::EVENT_POSTREGISTRATION => [
                ['emailUserCredentials'],
                ['checkIfNewUserHasGroupInvitations']
            ]
        ];
    }

    /**
     * Check if new user has pending invitations if so attach them to the group.
     * @param UserEvent $userEvent
     */
    public function checkIfNewUserHasGroupInvitations(UserEvent $userEvent)
    {
        $pendingInvitations = $this->entityManager->getRepository('AppBundle:GroupInvite')->findBy(['email' => $userEvent->getUser()->getEmail()]);

        if (!empty($pendingInvitations)){

            /** @var GroupInvite $invitation */
            foreach ($pendingInvitations as $invitation){
                $groupUser = new GroupUser();
                $groupUser->setUser($userEvent->getUser());
                $groupUser->setGroup($invitation->getGroup());

                $this->entityManager->persist($groupUser);
                $this->entityManager->flush($groupUser);
            }
        }
    }

    /**
     * @param UserEvent $userEvent
     */
    public function emailUserCredentials(UserEvent $userEvent)
    {
        $data = $userEvent->getData();
        $this->mailerService->send(
            $userEvent->getUser(), 'Your login details',
            '@User/Email/logindetails.html.twig',//'UserBundle:Email:logindetails.html.twig',
			array('user' => $userEvent->getUser())
        );
    }

    /**
     * @param UserEvent $userEvent
     * @return bool
     */
    protected function assertEventHasMasterPermissions(UserEvent $userEvent)
    {
        $data = $userEvent->getData();

        /** @var User|null $currentUser */
        $currentUser = isset($data['currentUser']) ? $data['currentUser'] : null;
        if (!$currentUser || !$currentUser->hasRole(User::ROLE_ADMIN)) {
            return false;
        }

        return true;
    }
}
