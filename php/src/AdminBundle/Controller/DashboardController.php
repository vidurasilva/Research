<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function indexAction(Request $request)
    {
        $data =
            [
                'stats' =>
                    [
                        'users' => $this->getDoctrine()->getRepository('UserBundle:User')->count(),
                        'goals' => $this->getDoctrine()->getRepository('AppBundle:Goal')->count()
                    ]
            ];

        return $this->render('AdminBundle:DashBoard:index.html.twig', $data);
    }
}
