<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\CommunityCategory;
use AppBundle\Entity\CommunityQuestion;
use AppBundle\Entity\CommunityQuestionAnswer;
use UserBundle\Entity\User;
use AdminBundle\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Community controller.
 *
 * @Route("/community")
 */
class CommunityController extends AbstractCrudController
{
	protected $templateOptions =
		[
			'overview' => 'AdminBundle:Community:overview.html.twig'
		];

	/**
	 * List overview
	 *
	 * @Route("/", name="community_overview")
	 * @Method("GET")
	 */
	public function overviewAction()
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('AppBundle:CommunityCategory')->findAll(); //@todo: add pagination to reduce overhead.

		return $this->render($this->templateOptions['overview'], [
			'entity' => $entity,
		]);
	}

	/**
	 * Creates a new Community entity.
	 *
	 * @Route("/create", name="community_create")
	 * @Method({"GET", "POST"})
	 */
	public function createAction(Request $request)
	{
		$community = new CommunityCategory();
		$form      = $this->createForm('AdminBundle\Form\Type\CommunityCategoryType', $community);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			if ($file = $request->files->get('community_category')['file']) {
				$fileName = $this->get('admin.file_uploader')->upload($file);
				$community->setIcon($fileName);
			}

			$em->persist($community);
			$em->flush();

			return $this->redirectToRoute('community_edit', ['id' => $community->getId()]);
		}

		return $this->render('AdminBundle:Community:create.html.twig', [
			'community' => $community,
			'form'      => $form->createView(),
		]);
	}

	/**
	 * Finds and displays a Community entity.
	 *
	 * @Route("/{id}", name="community_show")
	 * @Method("GET")
	 */
	public function showAction(CommunityCategory $community)
	{
		$deleteForm = $this->createDeleteForm($community);

		return $this->render('AdminBundle:Community:show.html.twig', [
			'community'   => $community,
			'delete_form' => $deleteForm->createView(),
		]);
	}

	/**
	 * Displays a form to edit an existing Community entity.
	 *
	 * @Route("/{id}/edit", name="community_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editAction(Request $request, CommunityCategory $Community)
	{
		$deleteForm = $this->createDeleteForm($Community);
		$editForm   = $this->createForm('AdminBundle\Form\Type\CommunityCategoryType', $Community);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$em = $this->getDoctrine()->getManager();

			if ($file = $request->files->get('community_category')['file']) {
				$fileName = $this->get('admin.file_uploader')->upload($file);
				$Community->setIcon($fileName);
			}

			$em->persist($Community);
			$em->flush();

			$this->addFlash('success', 'Community saved.');

			return $this->redirectToRoute('community_edit', ['id' => $Community->getId()]);
		}

		return $this->render('AdminBundle:Community:edit.html.twig', [
			'community'   => $Community,
			'edit_form'   => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
		]);
	}

	/**
	 * Deletes a Community entity.
	 *
	 * @Route("/{id}", name="community_delete")
	 * @Method("DELETE")
	 */
	public function deleteAction(Request $request, CommunityCategory $community)
	{
		$form = $this->createDeleteForm($community);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($community);
			$em->flush();
		}

		return $this->redirectToRoute('community_overview');
	}

	/**
	 * Creates a form to delete a Community entity.
	 *
	 * @param Community $community The Community entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(CommunityCategory $Community)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('community_delete', ['id' => $Community->getId()]))
			->setMethod('DELETE')
			->getForm();
	}


	/**
	 * List overview of questions
	 * @param CommunityCategory $communityCategory
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @Route("/{id}/questions", name="community_questions_overview")
	 * @Method("GET")
	 */
	public function overviewQuestionsAction(CommunityCategory $communityCategory)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('AppBundle:CommunityQuestion')->findBy([
			'communityCategory' => $communityCategory
		]);

		return $this->render('AdminBundle:Community:questions-overview.html.twig', [
			'entity'   => $entity,
			'category' => $communityCategory
		]);
	}

	/**
	 * Finds and displays a question detail
	 *
	 * @Route("/question/{id}", name="community_question_show")
	 * @Method("GET")
	 */
	public function showQuestionAction(CommunityQuestion $communityQuestion)
	{
		$deleteForm = $this->createQuestionDeleteForm($communityQuestion);

		return $this->render('AdminBundle:Community:question-show.html.twig', [
			'question'    => $communityQuestion,
			'delete_form' => $deleteForm->createView(),
		]);
	}

	/**
	 * Deletes a community question and all the underlying answers!
	 *
	 * @Route("/question/{id}/delete", name="community_question_delete")
	 * @Method("DELETE")
	 */
	public function deleteQuestionAction(Request $request, CommunityQuestion $question)
	{
		$form = $this->createQuestionDeleteForm($question);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($question);
			$em->flush();
		}

		return $this->redirectToRoute('community_overview');
	}

	/**
	 * Creates a form to delete a question.
	 *
	 * @param CommunityQuestion $communityQuestion The Community question entity.
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createQuestionDeleteForm(CommunityQuestion $communityQuestion)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('community_question_delete', ['id' => $communityQuestion->getId()]))
			->setMethod('DELETE')
			->getForm();
	}

	/**
	 * List overview of questions
	 * @param CommunityQuestion $communityQuestion
	 * @Route("/{id}/answers", name="community_answers_overview")
	 * @Method("GET")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function overviewAnswersAction(CommunityQuestion $communityQuestion)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('AppBundle:CommunityQuestionAnswer')->findBy(['questions' => $communityQuestion]);

		return $this->render('AdminBundle:Community:answers-overview.html.twig', [
			'entity'   => $entity,
			'question' => $communityQuestion
		]);
	}

	/**
	 * Deletes a community answers!
	 *
	 * @Route("/answer/{id}/delete", name="community_answer_delete")
	 * @Method("DELETE")
	 */
	public function deleteAnswerction(Request $request, CommunityQuestionAnswer $answer)
	{
		$form = $this->createAnswerDeleteForm($answer);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($answer);
			$em->flush();
		}

		return $this->redirectToRoute('community_overview');
	}

	/**
	 * Creates a form to delete a answer.
	 *
	 * @param CommunityQuestionAnswer $answer
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createAnswerDeleteForm(CommunityQuestionAnswer $answer)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('community_answer_delete', ['id' => $answer->getId()]))
			->setMethod('DELETE')
			->getForm();
	}

	/**
	 * Finds and displays a question detail
	 *
	 * @Route("/answer/{id}", name="community_answer_show")
	 * @Method("GET")
	 */
	public function showAnswerAction(CommunityQuestionAnswer $answer)
	{
		$deleteForm = $this->createAnswerDeleteForm($answer);

		return $this->render('AdminBundle:Community:answer-show.html.twig', [
			'answer'      => $answer,
			'delete_form' => $deleteForm->createView(),
		]);
	}
}
