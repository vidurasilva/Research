<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\PaymentCharge;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Payment controller
 *
 * @Route("/payment")
 */
class PaymentController extends AbstractCrudController
{
    protected $templateOptions =
        [
            'overview' => 'AdminBundle:Goal:overview.html.twig'
        ];

    /**
     * Payment export
     *
     * @Route("/export", name="payment_export")
     * @Method("GET")
     */
    public function overviewAction()
    {
        $data = [];
        $em   = $this->getDoctrine()->getManager();

        $payments = $em->getRepository('AppBundle:PaymentCharge')->findAll(); //Optimize this to reduce memory

        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        $csv->setDelimiter(';');

        $headers = [
            'id',
            'date',
            'firstname',
            'lastname',
            'goal',
            'group',
            'currency',
            'amount',
            'charity 1',
            'percentage',
            'charity 2',
            'percentage',
            'charity 3',
            'percentage'
        ];

        //First line
        $csv->insertOne($headers);

        /** @var PaymentCharge $payment */
        foreach ($payments as $payment) {

            $groupName = !empty($payment->getUserGoal()->getGroup()) ? $payment->getUserGoal()->getGroup()->getName() : '';
            $charities = [];

            foreach ($payment->getUserGoal()->getUserGoalCharities() as $charity) {
                $charities[] = ['title' => $charity->getCharity()->getTitle(), 'percentage' => $charity->getPercentage()];
            }

            $data[] = [
                $payment->getId(),
                $payment->getCreated()->format('Y-m-d'),
                $payment->getUser()->getFirstname(),
                $payment->getUser()->getLastname(),
                $payment->getUserGoal()->getGoal()->getTitle(),
                $groupName,
                $payment->getAmount(),
                $payment->getCurrency(),
                isset($charities[0]) ? $charities[0]['title'] : '',
                isset($charities[0]) ? $charities[0]['percentage'] : '',
                isset($charities[1]) ? $charities[1]['title'] : '',
                isset($charities[1]) ? $charities[1]['percentage'] : '',
                isset($charities[2]) ? $charities[2]['title'] : '',
                isset($charities[2]) ? $charities[2]['percentage'] : '',
            ];
        }

        $csv->insertAll($data);

        $csv->output(sprintf('Payment-export-%d.csv', time()));

        die;
    }
}
