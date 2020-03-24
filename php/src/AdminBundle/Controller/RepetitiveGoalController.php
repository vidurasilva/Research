<?php
/**
 * Created by PhpStorm.
 * User: Matthijs Overboom
 * Date: 5-1-17
 * Time: 9:36
 */

namespace AdminBundle\Controller;


use AppBundle\Entity\Goal;
use AppBundle\Entity\Task;
use AdminBundle\Form\Type\TaskBlueprintType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Form\Type\RepetitiveGoalType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class RepetitiveGoalController
 * @package AdminBundle\Controller
 *
 * @Route(path="/repetitivegoal")
 */
class RepetitiveGoalController extends AbstractCrudController
{

	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @Route(path="/create", name="repetitivegoal_create")
	 * @Method({"GET", "POST"})
	 */
	public function createAction(Request $request)
	{
		$goal = new Goal();
		$goalForm = $this->createForm(RepetitiveGoalType::class, $goal);
		$goalForm->handleRequest($request);

		if($goalForm->isSubmitted() && $goalForm->isValid()) {
			$em = $this->getDoctrine()->getManager();
			if($file = $request->files->get('repetitive_goal')['file']) {
				$fileName = $this->get('admin.file_uploader')->upload($file);
				$goal->setIcon($fileName);
			}

			$this->get('app.service.repetitive_goal')->generateRepetitiveGoalMileStones($goal, $this->getFormTask($goalForm, $request));
			$em->persist($goal);
			$em->flush();
			return $this->redirectToRoute('goal_edit', array(
				'id' => $goal->getId()
			));
		}

		return $this->render('AdminBundle:RepetitiveGoal:create.html.twig', array(
			'goalForm' => $goalForm->createView()
		));
	}

	/**
	 * Get Task from form
	 *
	 * @param Form $form
	 * @param Request $request
	 * @return Task
	 */
	private function getFormTask(Form $form, Request $request)
	{
		$task = new Task();
		$task->setTitle($form['taskTitle']->getData());
		$task->setDescription($form['taskDescription']->getData());
		$task->setPoints($form['taskPoints']->getData());
		$task->setVideo($form['taskVideo']->getData());

		if($file = $request->files->get('repetitive_goal')['taskImage']) {
			$fileName = $this->get('admin.file_uploader')->upload($file);
			$task->setImage($fileName);
		}

		return $task;
	}
}