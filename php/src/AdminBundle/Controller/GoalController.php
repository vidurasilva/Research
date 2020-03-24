<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Goal;
use AppBundle\Entity\Milestone;
use AppBundle\Entity\Task;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Goal controller
 *
 * @Route("/goal")
 */
class GoalController extends AbstractCrudController
{
    protected $templateOptions =
        [
            'overview' => 'AdminBundle:Goal:overview.html.twig'
        ];

    /**
     * List overview
     *
     * @Route("/", name="goal_overview")
     * @Method("GET")
     */
    public function overviewAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Goal')->findPredifined(); //@todo: add pagination to reduce overhead.

        return $this->render($this->templateOptions['overview'], [
            'entity' => $entity,
        ]);
    }


    /**
     * Creates a new Goal entity.
     *
     * @Route("/create", name="goal_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $goal = new Goal();
        $form = $this->createForm('AdminBundle\Form\Type\GoalType', $goal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
//			$durationUnit = $form['durationUnit']->getData();
//			if($goal->getDuration() > 1) {
//				$durationUnit .= 's';
//			}
//			$goal->setDuration($goal->getDuration() . ' ' . $durationUnit);
            if ($file = $request->files->get('goal')['file']) {
                $fileName = $this->get('admin.file_uploader')->upload($file);
                $goal->setIcon($fileName);
            }

            $goal->setPredefined(true);

            $goal = $this->handleTasksImages($goal, $form->getData());

            $em->persist($goal);
            $em->flush();

            return $this->redirectToRoute('goal_edit', ['id' => $goal->getId()]);
        }

        return $this->render('AdminBundle:Goal:create.html.twig', [
            'goal' => $goal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Goal entity.
     *
     * @Route("/{id}", name="goal_show")
     * @Method("GET")
     */
    public function showAction(Goal $goal)
    {
        $deleteForm = $this->createDeleteForm($goal);

        return $this->render('AdminBundle:Goal:show.html.twig', [
            'goal'        => $goal,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Goal entity.
     *
     * @Route("/{id}/edit", name="goal_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Goal $goal)
    {
        $deleteForm = $this->createDeleteForm($goal);
        $editForm   = $this->createForm('AdminBundle\Form\Type\GoalType', $goal);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

			if ($editForm->get('removeFile')->getData()) {
				$goal->setIcon(null);
			}

            if ($file = $request->files->get('goal')['file']) {
                $fileName = $this->get('admin.file_uploader')->upload($file);
                $goal->setIcon($fileName);
            }

	        $goal->setPredefined(true);

	        $goal = $this->handleTasksImages($editForm->getData());

            $em->persist($goal);
            $em->flush();

            $this->addFlash('success', 'Goal saved.');

            return $this->redirectToRoute('goal_edit', ['id' => $goal->getId()]);
        }

        return $this->render('AdminBundle:Goal:edit.html.twig', [
            'goal'        => $goal,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a goal entity.
     *
     *
     * @Route("/{id}", name="goal_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Goal $goal)
    {
        $form = $this->createDeleteForm($goal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($goal);
            $em->flush();
        }

        return $this->redirectToRoute('goal_overview');
    }

    /**
     * Creates a form to delete a goal entity.
     *
     * @param Goal $goal
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Goal $goal)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('goal_delete', ['id' => $goal->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Goal $newGoal
     * @return Goal
     */
    protected function handleTasksImages(Goal $newGoal)
    {
        $fileUploader = $this->get('admin.file_uploader');

        if (!empty($newGoal->getMilestones())) {
            /** @var Milestone $milestone */
            foreach ($newGoal->getMilestones() as $milestone) {

                if (!empty($milestone->getTasks())) {

                    /** @var Task $task */
                    foreach ($milestone->getTasks() as $task) {

                        if ($task->getImage() instanceof UploadedFile) {
                            $filename = $fileUploader->upload($task->getImage());
                            $task->setImage($filename);
                        } else {

                        	if ($task->getRemoveImage()) {
                        		$task->setImage(null);
							} else {
								// Get old image directly from database because unit of work
								$oldImage = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Task')->findTaskImage($task->getId());

								if (isset($oldImage['image'])) {
									$task->setImage($oldImage['image']);
								}
							}
                        }
                    }
                }
            }
        }

        return $newGoal;
    }
}
