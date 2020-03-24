<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/28/16
 * Time: 11:28 AM
 */

namespace AppBundle\Controller;

use ApiBundle\Responses\FailResponse;
use ApiBundle\Responses\SuccessResponse;
use AppBundle\Entity\PaymentCharge;
use AppBundle\Exception\Payment\CustomerNotFound;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ApiBundle\Controller\AbstractApiController;
use ApiBundle\Exception\FailRegistry;
use ApiBundle\Responses\GeneralFailResponse;
use AppBundle\Responses\StripeCustomerDetail;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class PaymentController extends AbstractApiController
{
	/**
	 * Create a customer via the stripe API.
	 * The response in the data attribute is the response we get from Stripe
	 * @ApiDoc(
	 *     section="Payment",
	 *     resource=true,
	 *     description="This call creates a customer via the Stripe API for the current user.",
	 *     output="ApiBundle\Responses\SuccessResponse",
	 *     tags={
	 *         "anonymous" = "#408000"
	 *     }
	 * )
	 *
	 * @param Request $request
	 * @return View
	 */
	public function getCustomerAction(Request $request)
	{
		$customer = null;
		$stripeService = $this->get('app.service.stripe_payment');
		/** @var User $user */
		$user = $this->getUser();
		try {
			if (!$user->getPaymentUser()) {
				// we need to create a user
				$customer = $stripeService->createCustomer($user);
			} else {
				$customer = $stripeService->retrieveCustomer($user->getPaymentUser());
			}
		} catch (\Exception $e) {
			return $this->createApiResponse(
				new GeneralFailResponse(FailRegistry::PAYMENT_GENERAL_STRIPE_ERROR, $e->getMessage())
			);
		}
		return $this->createApiResponse(
			new StripeCustomerDetail($customer)
		);
	}

	/**
	 * Store a source in the Stripe API.
	 * The response in the data attribute is the response we get from Stripe
	 * @ApiDoc(
	 *     section="Payment",
	 *     resource=true,
	 *     description="This call stores a source in the Stripe API for the current user.",
	 *     output="ApiBundle\Responses\SuccessResponse",
	 *     tags={
	 *         "anonymous" = "#408000"
	 *     },
	 *     parameters={
	 *          { "name"="source", "dataType"="string", "required"=true, "description"="ID of the Stripe source"}
	 *      },
	 * )
	 *
	 * @param Request $request
	 * @return View
	 */
	public function storeSourceAction(Request $request)
	{
		/** @var User $user */
		$user = $this->getUser();
		$stripeService = $this->get('app.service.stripe_payment');
		$source = $request->request->get('source');
		if (!$source) {
			return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS, ['source']));
		}
		try {
			$stripeService->storeSource($user->getPaymentUser(), $source);
		} catch (CustomerNotFound $e) {
			return $this->createApiResponse(
				new GeneralFailResponse(FailRegistry::PAYMENT_GENERAL_STRIPE_ERROR, 'Customer not found for user, did your forget to create one?')
			);
		} catch (\Exception $e) {
			return $this->createApiResponse(
				new GeneralFailResponse(FailRegistry::PAYMENT_GENERAL_STRIPE_ERROR, $e->getMessage())
			);
		}

		return $this->createApiResponse(new SuccessResponse('Source stored for user'));
	}

}