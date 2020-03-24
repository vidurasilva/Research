<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Entity\UserGoal;
use AppBundle\Model\Status;
use Doctrine\ORM\EntityManager;
use UserBundle\Entity\User;
use AdminBundle\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("/usergoal")
 */
class UserGoalController extends AbstractCrudController
{
    protected $templateOptions =
        [
            'overview' => 'AdminBundle:UserGoal:overview.html.twig',
            'edit'     => 'AdminBundle:UserGoal:edit.html.twig',
        ];

	/**
	 * List overview
	 *
	 * @Route("/", name="usergoal_overview")
	 * @Method("GET")
	 */
	public function overviewAction()
	{
		$em = $this->getDoctrine()->getManager();

		$userGoals = $em->getRepository('AppBundle:UserGoal')->findAll();

		return $this->render($this->templateOptions['overview'], array(
			'userGoals' => $userGoals,
		));
	}

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="usergoal_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('AdminBundle:CRUD:show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing userGoal entity.
     *
     * @Route("/{id}/edit", name="usergoal_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, UserGoal $userGoal)
    {
        //$deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AdminBundle\Form\Type\UserGoalType', $userGoal);
        $editForm->handleRequest($request);

	    /** @var EntityManager $em */
	    $em = $this->getDoctrine()->getManager();

	    $checkins = $em->getRepository('AppBundle:Checkin')->findBy(['userGoal' => $userGoal]);

	    foreach($userGoal->getGoal()->getMilestones() as $milestone){
	    	/** @var Task $task */
		    foreach($milestone->getTasks() as $task){
			    if ($em->getRepository('AppBundle:Checkin')->findOneBy(['userGoal' => $userGoal, 'task' => $task, 'status' => Status::DONE])){
			    	//Mark task as done
				    $task->setTempStatus(true);
			    }
		    }
	    }


	    if ($editForm->isSubmitted() ) {
            $em->persist($userGoal);
            $em->flush();

            $this->addFlash('success', 'User goal saved.');

            return $this->redirectToRoute('usergoal_edit', array('id' => $userGoal->getId()));
        }

        return $this->render('AdminBundle:UserGoal:edit.html.twig', array(
            'userGoal' => $userGoal,
            'edit_form' => $editForm->createView()
        ));
    }
}
