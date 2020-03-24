<?php

namespace AdminBundle\Controller;

use UserBundle\Entity\User;
use AdminBundle\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends AbstractCrudController
{

    protected $templateOptions = [
      'overview' => 'AdminBundle:User:overview.html.twig',
    ];

    /**
     * Creates a new User entity.
     *
     * @Route("/create", name="user_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user->setUsername($user->getEmail());

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show',
              ['id' => $user->getId()]);
        }

        return $this->render('AdminBundle:CRUD:create.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('AdminBundle:CRUD:show.html.twig', [
          'user' => $user,
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AdminBundle\Form\Type\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user->setPlainPassword($user->getPassword());

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User saved.');

            return $this->redirectToRoute('user_edit',
              ['id' => $user->getId()]);
        }

        return $this->render('AdminBundle:CRUD:edit.html.twig', [
          'user' => $user,
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($user);
            $em->flush($user);
        }

        return $this->redirectToRoute('user_overview');
    }


    /**
     * Creates a form to delete a User entity.
     *
     * @param User $user The User entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
          ->setAction($this->generateUrl('user_delete',
            ['id' => $user->getId()]))
          ->setMethod('DELETE')
          ->getForm();
    }
}
