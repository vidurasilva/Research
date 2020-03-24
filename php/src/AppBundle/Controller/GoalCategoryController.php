<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/12/16
 * Time: 2:17 PM
 */

namespace AppBundle\Controller;

use AppBundle\Responses\CategoryList;
use AppBundle\Responses\GoalList;
use AppBundle\Entity\GoalCategory;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiBundle\Controller\AbstractApiController;

class GoalCategoryController extends AbstractApiController
{
	/**
	 * @ApiDoc(
	 *     section="Goals",
	 *     resource=true,
	 *     description="This call returns the predefined categories.",
	 *     output="ApiBundle\Responses\SuccessResponse",
	 *     tags={
	 *         "anonymous" = "#408000"
	 *     }
	 * )
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function indexAction(Request $request)
	{
		$categories = $this->getDoctrine()->getRepository('AppBundle:GoalCategory')->findAll();
		$uploadPath = $this->getParameter('data_upload_web');
		$uploadPath = $request->getSchemeAndHttpHost() . $request->getBaseUrl() . $uploadPath . '/';//getBaseUrl()
		return $this->createApiResponse(new CategoryList($categories, $uploadPath), 'list');
	}

	/**
	 * @ApiDoc(
	 *     section="Goals",
	 *     resource=true,
	 *     description="This call returns the goals within the selected category.",
	 *     output="ApiBundle\Responses\SuccessResponse",
	 *     tags={
	 *         "anonymous" = "#408000"
	 *     }
	 * )
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function detailAction(Request $request, GoalCategory $category)
	{
		$goals = $this->getDoctrine()->getRepository('AppBundle:Goal')->findBy(['category' => $category->getId(), 'predefined' => true]);
		$uploadPath = $this->getParameter('data_upload_web');
		$uploadPath = $request->getSchemeAndHttpHost() . $request->getBaseUrl() . $uploadPath . '/';
		return $this->createApiResponse(new GoalList($goals, $uploadPath), 'list');
	}
}