<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function homepageAction()
	{
		return $this->render('AppBundle::homepage.html.twig');
	}

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function termsAndConditionsAction()
    {
        return $this->render('AppBundle:Page:terms-and-conditions.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction()
    {
        return $this->render('AppBundle:Page:about.html.twig');
    }

     /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function joinGroupAction(Request $request)
    {
        $parameters = $request->query;
        $goalUrl = $parameters->get('url');
       
        $decodedUrl =base64_decode($goalUrl);
        $startDate = end(split('/',$decodedUrl));
        $d = date('Y-m-d', strtotime($startDate));
        $today = date('Y-m-d');
        
        if ($today>$d)
            return $this->render('AppBundle:Page:join-group-failed.html.twig');
        else
            return $this->render('AppBundle:Page:join-group.html.twig', ['url' => $decodedUrl]);
       
    }

    /**
     * @param Request $request
     * @param $recoveryToken
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updatePasswordAction(Request $request, $recoveryToken)
    {
        if (empty($recoveryToken)) {
            throw new AccessDeniedHttpException('Recovery token not provided');
        }

        $user = $this->getUserService()->findUserByConfirmationToken($recoveryToken);
        if (!$user || !$user->isPasswordRequestNonExpired($this->getRecoveryTokenTTL())) {
            throw new AccessDeniedHttpException('Recovery token is expired.');
        }

        $form = $this->createForm('AppBundle\Form\Type\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user->setConfirmationToken(null);
            $this->getUserService()->updateUser($user);

            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'Password changed.');
            return $this->redirectToRoute('user_password_reset_success');
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Password change failed');
        }

        return $this->render('AppBundle:User:change-password.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePasswordSuccessAction(Request $request)
    {
        return $this->render('AppBundle:User:change-password-success.html.twig', []);
    }

    /**
     * @return object|\UserBundle\Service\UserService
     */
    protected function getUserService()
    {
        return $this->get('user.service');
    }

    /**
     * @return integer
     */
    protected function getRecoveryTokenTTL()
    {
        return $this->container->getParameter('fos_user.resetting.token_ttl');
    }
}
