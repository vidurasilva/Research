<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CommunityCategory;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Responses\CommunityCategoryList;
use ApiBundle\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommunityCategoryController extends AbstractApiController
{
    /**
     * @ApiDoc(
     *     section="Community",
     *     resource=true,
     *     description="This call returns the community categories.",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @return View
     */
    public function indexAction(Request $request)
    {
        $categories = $this->getDoctrine()->getRepository('AppBundle:CommunityCategory')->findBy(['parent' => null]);

        return $this->createApiResponse(new CommunityCategoryList($categories, $this->generateBaseUrl($request), 0));
    }

    /**
     * @ApiDoc(
     *     section="Community",
     *     resource=true,
     *     description="This call returns the sub-categories.",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @return View
     */
    public function detailAction(Request $request, CommunityCategory $communityId)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$subcategories = $em->getRepository('AppBundle:CommunityCategory')->findBy(['parent' => $communityId])){
            throw new NotFoundHttpException('Category not found.');
        }

       // $totalQuestions = $em->getRepository('AppBundle:CommunityQuestion')->countByCategory($communityId);

        return $this->createApiResponse(new CommunityCategoryList($subcategories, $this->generateBaseUrl($request), 0));
    }
}