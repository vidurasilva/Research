<?php

namespace AppBundle\Event;

use AppBundle\Entity\GoalGroup;
use AppBundle\Entity\GroupInvite;
use AppBundle\Entity\GroupUser;
use AppBundle\Service\MailerService;
use Doctrine\ORM\EntityManager;
use UserBundle\Service\UserService;

/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 11/10/16
 * Time: 10:13
 */
class GoalSubscribeListener
{

    /**
     * @var MailerService
     */
    protected $mailService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * GoalSubscribeListener constructor.
     *
     * @param \AppBundle\Service\MailerService $mailerService
     * @param \UserBundle\Service\UserService $userService
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(
      MailerService $mailerService,
      UserService $userService,
      EntityManager $entityManager
    ) {
        $this->mailService = $mailerService;
        $this->userService = $userService;
        $this->em = $entityManager;
    }

    /**
     * Check if user exist and if exist add to group
     *
     * @param GoalEvent $goalEvent
     */
    public function attachExistingUserToGroupGoal(GoalEvent $goalEvent)
    {
        $goalGroup = $goalEvent->getUserGoal()->getGroup();

        if ($goalGroup instanceof GoalGroup) {

            /** @var GroupInvite $invitation */
            foreach ($goalGroup->getInvitations() as $invitation) {
                if (filter_var($invitation->getEmail(),
                  FILTER_VALIDATE_EMAIL)) {
                    if ($user = $this->userService->findUserByEmail($invitation->getEmail())) {
                        $groupUser = new GroupUser();
                        $groupUser->setUser($user);
                        $groupUser->setGroup($goalGroup);

                        $goalGroup->addGroupUser($groupUser);
                    }
                }
            }
        }
    }

    /**
     * Send group invites using the mail service.
     *
     * @param GoalEvent $goalEvent
     */
    public function sendEmails(GoalEvent $goalEvent)
    {
        $goalGroup = $goalEvent->getUserGoal()->getGroup();
        if ($goalGroup instanceof GoalGroup) {
            /** @var GroupInvite $invitation */
            foreach ($goalGroup->getInvitations() as $invitation) {

                if (!$invitation->getMailSended()) {
                    if ($this->mailService->sendToEmail($invitation->getEmail(),
                      'Goal invite', 'AppBundle:Goal:invite.html.twig', [
                        'start_date' => $goalEvent->getUserGoal()
                          ->getStartDate()
                          ->format('Y-m-d'),
                      ])) {
                        $invitation->setMailAttemps(+1);
                        $invitation->setMailSended(1);
                    } else {
                        $invitation->setMailSended(false);
                    }
                }
            }
            $this->em->flush();
        }
    }
}