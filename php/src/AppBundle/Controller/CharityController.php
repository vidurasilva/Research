<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/26/16
 * Time: 11:34 AM
 */

namespace AppBundle\Controller;

use AppBundle\Responses\CharityCategoryList;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ApiBundle\Controller\AbstractApiController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CharityController extends AbstractApiController
{

	/**
	 * List all available charities, grouped by category
	 * @ApiDoc(
	 *     section="Goals",
	 *     resource=true,
	 *     description="This call returns the available charities.",
	 *     output="ApiBundle\Responses\SuccessResponse",
	 *     tags={
	 *         "anonymous" = "#408000"
	 *     }
	 * )
	 *
	 * @param Request $request
	 * @return View
	 */
	public function indexAction()
	{
		$categories = $this->getDoctrine()->getRepository('AppBundle:CharityCategory')->findAll();

		return $this->createApiResponse(new CharityCategoryList($categories), 'list');
	}
}