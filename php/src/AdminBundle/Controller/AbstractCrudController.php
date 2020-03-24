<?php
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 06/09/16
 * Time: 15:01
 */

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractCrudController extends Controller
{

    /**
     * @var array
     */
    protected $templateOptions =
        [
            'overview' => 'AdminBundle:CRUD:overview.html.twig',
            'edit'     => 'AdminBundle:CRUD:edit.html.twig',
        ];

    /**
     * @var
     */
    protected $entity;

    /**
     * List overview
     *
     * @Route("/", name="user_overview")
     * @Method("GET")
     */
    public function overviewAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('UserBundle:User')->findAll();

        return $this->render($this->templateOptions['overview'], array(
            'users' => $users,
        ));
    }

    /**
     * @param null $id
     * @return object
     */
    public function getEntity($id = null)
    {
        if (empty($this->entityName)) {
            $this->entityName = str_replace('Controller', '', str_replace('AdminBundle\\Controller\\', '', get_class($this))); //@todo: change this to a more efficient way
        }

        return $this->resolveEntityByName($this->entityName, $id);
    }

    /**
     * @param $entityName
     * @param null $id
     * @return object
     */
    public function resolveEntityByName($entityName, $id = null)
    {
        if (!empty($id)) {
            return $this->getDoctrine()->getRepository($entityName)->find($id);
        }

        $entityName = sprintf('UserBundle\Entity\%s', $entityName);
        $entity = new $entityName;

        return $entity;
    }
}