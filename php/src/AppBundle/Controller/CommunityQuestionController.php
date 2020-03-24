<?php

namespace AppBundle\Controller;

use ApiBundle\Controller\AbstractApiController;
use ApiBundle\Exception\ApiException;
use ApiBundle\Responses\SuccessResponse;
use AppBundle\Entity\CommunityQuestionVote;
use AppBundle\Form\Type\CommunityQuestionType;
use AppBundle\Repository\CommunityQuestionRepository;
use AppBundle\Responses\CommunityQuestionDetail;
use AppBundle\Responses\CommunityQuestionSummary;
use AppBundle\Entity\CommunityQuestion;
use AppBundle\Responses\CommunityQuestionList;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommunityQuestionController extends AbstractApiController
{
    /**
     * @ApiDoc(
     *     section="Community",
     *     resource=true,
     *     description="This call fetches all the questions within the category.",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     parameters={
     *          { "name"="offset", "dataType"="integer", "required"=true, "description"="Offset"},
     *          { "name"="limit", "dataType"="integer", "required"=true, "description"="Limit"},
     *          { "name"="sort", "dataType"="string", "required"=false, "description"="Sort results on most recent or top rated"},
     *     },
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @param $id
     *
	 * @return Response
     * @throws ApiException
     */
    public function indexAction(Request $request, $id)
    {
        $em = $this->getDoctrine();

        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 10);
	    $sort = $request->query->get('sort', CommunityQuestionRepository::SORT_MOST_RECENT);

        $category = $em->getRepository('AppBundle:CommunityCategory')->find($id);

        $questions = $em->getRepository('AppBundle:CommunityQuestion')->findPaginated($category, $offset, $limit, $sort);

        $total = $em->getRepository('AppBundle:CommunityQuestion')->count($category);

        return $this->createApiResponsePaginated(new CommunityQuestionList($questions, $this->generateBaseUrl($request), $offset, $limit, $total, 'list'));
    }

    /**
     * @ApiDoc(
     *     section="Community",
     *     resource=true,
     *     description="This call fetch the detail of a question.",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @param $id
     * @return View
     * @throws ApiException
     */
    public function detailAction(Request $request, CommunityQuestion $question)
    {
        return $this->createApiResponse(new CommunityQuestionDetail($question, $this->generateBaseUrl($request)), 'details');
    }

    /**
     * @ApiDoc(
     *     section="Community",
     *     resource=true,
     *     description="This call creates a new question within a category.",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     parameters={
     *          { "name"="question", "dataType"="string", "required"=true, "description"="Question"}
     *     },
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @param $id
     * @return View
     * @throws ApiException
     */
    public function createAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$category = $em->getRepository('AppBundle:CommunityCategory')->find($id)) {
            throw new NotFoundHttpException('Category not found.');
        }

        $communityQuestion = new CommunityQuestion();
        $form = $this->createForm(CommunityQuestionType::class, $communityQuestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $communityQuestion->setUser($this->getUser());
            $communityQuestion->setCommunityCategory($category);

            $em->persist($communityQuestion);
            $em->flush();
        }

        if ($form->getErrors()->count() > 0) {
            throw new BadRequestHttpException('Form validation errors.');
        }

        return $this->createApiResponse(new CommunityQuestionSummary($form->getData(), $this->generateBaseUrl($request)));
    }

    /**
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="This call fetches all the questions from an user.",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     parameters={
     *          { "name"="offset", "dataType"="integer", "required"=true, "description"="Offset"},
     *          { "name"="limit", "dataType"="integer", "required"=true, "description"="Limit"},
     *     },
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @return View
     */
    public function indexUserAction(Request $request)
    {
        $em = $this->getDoctrine();

        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 10);

        $questions = $em->getRepository('AppBundle:CommunityQuestion')->findPaginatedByUser($this->getUser(), $offset, $limit);

        $total = $em->getRepository('AppBundle:CommunityQuestion')->countByUser($this->getUser());

        return $this->createApiResponsePaginated(new CommunityQuestionList($questions, $this->generateBaseUrl($request), $offset, $limit, $total, 'list'));
    }

	/**
	 * @ApiDoc(
	 *     section="Community",
	 *     resource=true,
	 *     description="Upvote or downvote a question",
	 *     output="ApiBundle\Responses\SuccessResponse",
	 *     parameters={
	 *          { "name"="vote", "dataType"="integer", "required"=true, "description"="For upvote use 1, for downvote use -1"}
	 *     },
	 *     tags={
	 *         "anonymous" = "#408000"
	 *     }
	 * )
	 *
	 * @param Request           $request
	 * @param CommunityQuestion $question
	 *
	 * @return View
	 * @internal param $answerId
	 *
	 */
	public function voteAction(Request $request, CommunityQuestion $question)
	{
		$em = $this->getDoctrine()->getManager();

		$vote = filter_var($request->get('vote', true), FILTER_VALIDATE_INT);


		//Search if user already voted on this question, otherwise add vote.
		if ($answers = $em->getRepository('AppBundle:CommunityQuestionVote')->findBy(['questions' => $question, 'user' => $this->getUser()])) {
			return $this->createApiResponse(new SuccessResponse('Already voted.'));
		}

		$questionVote = new CommunityQuestionVote();
		$questionVote->setQuestions($question);
		$questionVote->setUser($this->getUser());
		$questionVote->setVote($vote);

		$em->persist($questionVote);
		$em->flush();

		return $this->createApiResponse(new SuccessResponse('Vote added.'));
	}
}
