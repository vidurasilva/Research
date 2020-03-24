<?php

namespace AppBundle\Controller;

use ApiBundle\Controller\AbstractApiController;
use ApiBundle\Responses\SuccessResponse;
use AppBundle\Entity\CommunityQuestionAnswerVote;
use AppBundle\Form\Type\CommunityAnswerType;
use AppBundle\Responses\CommunityAnswerList;
use FOS\RestBundle\View\View;
use ApiBundle\Exception\ApiException;
use AppBundle\Entity\CommunityQuestion;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\CommunityQuestionAnswer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ApiBundle\Responses\CommunityAnswerSummary;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommunityAnswerController extends AbstractApiController
{
    /**
     * @ApiDoc(
     *     section="Community",
     *     resource=true,
     *     description="This call fetches all the answers from a question",
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
     * @param $questionId
     * @return Response
     * @throws ApiException
     */
    public function indexAction(Request $request, CommunityQuestion $questionId)
    {
        $em = $this->getDoctrine();

        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 10);

        $answers = $em->getRepository('AppBundle:CommunityQuestionAnswer')->findPaginated($questionId->getId(), $offset, $limit);

        $total = $em->getRepository('AppBundle:CommunityQuestionAnswer')->count($questionId);

        return $this->createApiResponsePaginated(new CommunityAnswerList($answers, $this->generateBaseUrl($request), $offset, $limit, $total, 'list'));
    }

    /**
     * @ApiDoc(
     *     section="Community",
     *     resource=true,
     *     description="This call creates an answer based on the question.",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     parameters={
     *          { "name"="answer", "dataType"="string", "required"=true, "description"="Answer"}
     *     },
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @param $questionId
     * @return View
     * @throws ApiException
     */
    public function createAction(Request $request, CommunityQuestion $questionId)
    {
        $em = $this->getDoctrine()->getManager();

        $communityAnswer = new CommunityQuestionAnswer();
        $form = $this->createForm(CommunityAnswerType::class, $communityAnswer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $communityAnswer->setUser($this->getUser());
            $communityAnswer->setQuestions($questionId);

            $em->persist($communityAnswer);
            $em->flush();
        }

        if ($form->getErrors()->count() > 0) {
            throw new BadRequestHttpException('Form validation errors.');
        }

        return $this->createApiResponse(new \AppBundle\Responses\CommunityAnswerSummary($form->getData(), $this->generateBaseUrl($request)));
    }

    /**
     * @ApiDoc(
     *     section="Community",
     *     resource=true,
     *     description="Create a new comment for an answer.",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     parameters={
     *          { "name"="answer", "dataType"="string", "required"=true, "description"="Answer"}
     *     },
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @param $answerId
     * @return View
     * @throws ApiException
     */
    public function createCommentAction(Request $request, CommunityQuestionAnswer $answerId)
    {
        $em = $this->getDoctrine()->getManager();

        $communityAnswer = new CommunityQuestionAnswer();
        $form = $this->createForm(CommunityAnswerType::class, $communityAnswer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $communityAnswer->setParent($answerId);
            $communityAnswer->setUser($this->getUser());
            $communityAnswer->setQuestions($answerId->getQuestions());

            $em->persist($communityAnswer);
            $em->flush();
        }

        if ($form->getErrors()->count() > 0) {
            throw new BadRequestHttpException('Form validation errors.');
        }

        return $this->createApiResponse(new \AppBundle\Responses\CommunityAnswerSummary($form->getData(), $this->generateBaseUrl($request)));
    }

    /**
     * @ApiDoc(
     *     section="Community",
     *     resource=true,
     *     description="Upvote or downvote an answer",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     parameters={
     *          { "name"="vote", "dataType"="integer", "required"=true, "description"="For upvote use 1, for downvote use -1"}
     *     },
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @param $answerId
     * @return View
     * @throws ApiException
     */
    public function voteAction(Request $request, CommunityQuestionAnswer $answerId)
    {
        $em = $this->getDoctrine()->getManager();

        $vote = filter_var($request->get('vote', true), FILTER_VALIDATE_INT);

        //Search if user already voted on this answer, otherwise add vote.
        if ($answers = $em->getRepository('AppBundle:CommunityQuestionAnswerVote')->findBy(['answers' => $answerId, 'user' => $this->getUser()])) {
            return $this->createApiResponse(new SuccessResponse('Already voted.'));
        }

        $answerVote = new CommunityQuestionAnswerVote();
        $answerVote->setAnswers($answerId);
        $answerVote->setUser($this->getUser());
        $answerVote->setVote($vote);

        $em->persist($answerVote);
        $em->flush();

        return $this->createApiResponse(new SuccessResponse('Vote added.'));
    }
}
