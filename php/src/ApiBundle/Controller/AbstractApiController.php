<?php

namespace ApiBundle\Controller;

use ApiBundle\Responses\AbstractResponse;
use ApiBundle\Responses\OffsetPaginatedResponse;
use AppBundle\Service\GoalService;
use AppBundle\Service\ScoreService;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Service\UserService as UserService;

abstract class AbstractApiController extends FOSRestController
{
    const STATUS_SUCCESS = 200;
    const STATUS_FAIL = 400;

	const TIMEZONE_HEADER_PARAM = 'client-timezone';

	protected function getTimezoneFromRequest()
	{
		$request = $this->get('request_stack')->getMasterRequest();
		$timezone = null;
		if ($request->headers->has(self::TIMEZONE_HEADER_PARAM)) {
			$timezoneName = $request->headers->get(self::TIMEZONE_HEADER_PARAM);
			try {
				$timezone = new \DateTimeZone($timezoneName);
			} catch (\Exception $e) {
			}
		}

		if (!$timezone) {
			$timezone = new \DateTimeZone(date_default_timezone_get());
		}

		return $timezone;
	}

	protected function getClientDateTime()
	{
		$clientDate = new \DateTime();
		$clientDate->setTimezone($this->getTimezoneFromRequest());
		return $clientDate;
	}

	protected function getWebUploadPath()
	{
		$request = $this->get('request_stack')->getMasterRequest();
		return $request->getSchemeAndHttpHost() . $this->getParameter('data_upload_web');
	}

    /**
     * @param AbstractResponse $data
     * @return View
     */
    protected function createApiResponse(AbstractResponse $data, $serializationGroups = [])
    {
    	if (!is_array($serializationGroups)) {
    		$serializationGroups = [$serializationGroups];
	    }
	    $view = View::create($data, $data->getStatusCode());
    	if (count($serializationGroups)) {
		    $context = new Context();
		    foreach ($serializationGroups as $group) {
			    $context->addGroup($group);
		    }
		    $view->setContext($context);
	    }
	    return $this->handleView($view);
    }

    /**
     * @param OffsetPaginatedResponse $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function createApiResponsePaginated(OffsetPaginatedResponse $data)
    {
        return $this->handleView(
            View::create($data, $data->getStatusCode())
        );
    }

    /**
     * @param array $params
     * @return array
     */
    protected function assertParameters(array $params)
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $missing = [];

        foreach ($params as $param) {
            if (!$request->request->has($param) && !$request->query->has($param) && !$request->attributes->has($param)) {
                $missing[] = $param;
            }
        }

        return $missing;
    }

    /**
     * Checks if
     * @return string
     */
    protected function getAccessTokenFromRequest()
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();

        $authorization = $request->headers->has('Authorization') ? $request->headers->get('Authorization') : null;
        $authorization = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $authorization));

        return $authorization ?: null;
    }

    protected function generateBaseUrl(Request $request)
    {
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . $this->getParameter('data_upload_web') . '/';

        return $baseurl;
    }


	/**
	 * @return GoalService
	 */
	protected function getGoalService()
	{
		return $this->get('app.service.goal');
	}

	/**
	 * @return object|\Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher|\Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher
	 */
	protected function getEventDispatcher()
	{
		return $this->get('event_dispatcher');
	}

	/**
	 * @return object|UserService
	 */
	protected function getUserService()
	{
		return $this->get('user.service');
	}

	/**
	 * @return ScoreService
	 */
	protected function getScoreService()
	{
		return $this->get('app.service.score_service');
	}
}
