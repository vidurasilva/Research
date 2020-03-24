<?php
/**
 * Created by PhpStorm.
 * User: Matthijs Overboom
 * Date: 3-1-17
 * Time: 13:29
 */

namespace AdminBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use UserBundle\Entity\Client;

/**
 * Client Controller
 *
 * Class ClientController
 * @package AdminBundle\Controller
 * @Route("/client")
 */
class ClientController extends Controller
{
	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @Route("/", name="client_overview")
	 * @Method({"GET"})
	 */
	public function overviewAction()
	{
		$clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
		return $this->render('AdminBundle:Client:overview.html.twig', [
			'clients' => $clients
		]);
	}
}