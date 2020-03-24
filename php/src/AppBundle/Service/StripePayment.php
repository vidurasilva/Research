<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/28/16
 * Time: 11:37 AM
 */

namespace AppBundle\Service;


use AppBundle\Entity\PaymentCharge;
use AppBundle\Exception\Payment\CardNotFound;
use AppBundle\Exception\Payment\Charge;
use AppBundle\Exception\Payment\CustomerNotFound;
use AppBundle\Exception\Payment\SourceNotFound;
use Doctrine\ORM\EntityManager;
use UserBundle\Entity\User;

class StripePayment
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	public function __construct($apiKey, EntityManager $entityManager)
	{
		\Stripe\Stripe::setApiKey($apiKey);
		$this->entityManager = $entityManager;
	}

	/**
	 * @param User $user
	 * @return \Stripe\Customer
	 * @throws \Exception
	 */
	public function createCustomer(User $user)
	{
		if ($user->getPaymentUser()) {
			throw new \Exception('User already has a payment customer');
		}

		$customer = \Stripe\Customer::create(
			[
				'description' => 'Customer for '.$user->getEmail(),
				'email' => $user->getEmail(),
				'metadata' => [
					'id' => $user->getId(),
					'name' => trim($user->getFirstname().' '.$user->getLastname()),
				]
			]
		);

		$user->setPaymentUser($customer->id);
		$this->entityManager->flush();

		return $customer;
	}

	/**
	 * @param string $stripeCustomerId
	 * @return \Stripe\Customer
	 * @throws CustomerNotFound
	 */
	public function retrieveCustomer($stripeCustomerId)
	{
		try {
			return \Stripe\Customer::retrieve($stripeCustomerId);
		} catch (\Exception $e) {
			throw new CustomerNotFound($e->getMessage());
		}
	}


	public function retrieveSource($stripeSourceId)
	{
		try {
			return \Stripe\Source::retrieve($stripeSourceId);
		} catch (\Exception $e) {
			throw new SourceNotFound($e->getMessage());
		}
	}

	public function retreiveCard($stripeCustomerId, $stripeCardId)
	{
		$customer = $this->retrieveCustomer($stripeCustomerId);
		try {
			return $customer->sources->retrieve($stripeCardId);
		} catch (\Exception $e) {
			throw new CardNotFound($e->getMessage());
		}
	}

	public function storeSource($stripeCustomerId, $source)
	{
		$customer = $this->retrieveCustomer($stripeCustomerId);

		return $customer->sources->create(
			[
				'source' => $source,
			]
		);
	}

	public function createPayment(PaymentCharge $paymentCharge)
	{
		try {
			$result = \Stripe\Charge::create(
				[
					'amount' => $paymentCharge->getAmount(),
					'customer' => $paymentCharge->getCustomer(),
					'currency' => $paymentCharge->getCurrency(),
					'source' => $paymentCharge->getSource(),
					'description' => $paymentCharge->getDescription(),
//					'capture' => false,
				]
			);
		} catch (\Exception $e) {
			throw new Charge($e->getMessage());
		}

		$paymentCharge->setStripeResponse($result->getLastResponse()->body);
		$paymentCharge->setCaptured($result->captured);
		$paymentCharge->setStripeChargeId($result->id);
	}

	public function getChargeAmount($amount, $currency)
	{
		if (!in_array(strtoupper($currency), ['EUR', 'USD'])) {
			throw new \InvalidArgumentException('Invalid currency');
		}
		// for now we only accept EUR and USD. The amount must be in cents, our amount is a decimal, so amount * 100
		return $amount * 100;
	}
}