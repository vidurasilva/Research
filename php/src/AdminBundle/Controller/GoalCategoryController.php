<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\GoalCategory;
use UserBundle\Entity\User;
use AdminBundle\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Goal category controller.
 *
 * @Route("/category")
 */
class GoalCategoryController extends AbstractCrudController
{
    protected $templateOptions =
        [
            'overview' => 'AdminBundle:Category:overview.html.twig'
        ];

    /**
     * List overview
     *
     * @Route("/", name="category_overview")
     * @Method("GET")
     */
    public function overviewAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:GoalCategory')->findAll(); //@todo: add pagination to reduce overhead.

        return $this->render($this->templateOptions['overview'], array(
            'entity' => $entity,
        ));
    }


    /**
     * Creates a new User entity.
     *
     * @Route("/create", name="category_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $category = new GoalCategory();
        $form = $this->createForm('AdminBundle\Form\Type\CategoryType', $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($file = $request->files->get('category')['file']) {
                $fileName = $this->get('admin.file_uploader')->upload($file);
                $category->setIcon($fileName);
            }

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_edit', array('id' => $category->getId()));
        }

        return $this->render('AdminBundle:Category:create.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="category_show")
     * @Method("GET")
     */
    public function showAction(GoalCategory $category)
    {
        $deleteForm = $this->createDeleteForm($category);

        return $this->render('AdminBundle:Category:show.html.twig', array(
            'category' => $category,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="category_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, GoalCategory $category)
    {
        $deleteForm = $this->createDeleteForm($category);
        $editForm = $this->createForm('AdminBundle\Form\Type\CategoryType', $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($file = $request->files->get('category')['file']) {
                $fileName = $this->get('admin.file_uploader')->upload($file);
                $category->setIcon($fileName);
            }

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Category saved.');

            return $this->redirectToRoute('category_edit', array('id' => $category->getId()));
        }

        return $this->render('AdminBundle:Category:edit.html.twig', array(
            'category' => $category,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, GoalCategory $category)
    {
        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('category_overview');
    }

    /**
     * Creates a form to delete a User entity.
     *
     * @param User $user The User entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(GoalCategory $category)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('category_delete', array('id' => $category->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
