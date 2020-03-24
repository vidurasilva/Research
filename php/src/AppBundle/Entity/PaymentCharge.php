<?php

namespace AppBundle\Entity;

use UserBundle\Entity\User;

/**
 * PaymentCharge
 */
class PaymentCharge
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var integer
     */
    private $amount;

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $stripeChargeId;

    /**
     * @var boolean
     */
    private $captured = 0;

    /**
     * @var boolean
     */
    private $paid = 0;

    /**
     * @var string
     */
    private $stripeResponse;

    /**
     * @var \UserBundle\Entity\User
     */
    private $user;

	public function __construct(User $user, $amount, UserGoal $userGoal)
	{
		$this->setUser($user);
		$this->setAmount($amount);
		$this->setCurrency($userGoal->getCurrency());
		$this->setUserGoal($userGoal);
	}

	/**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return PaymentCharge
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return PaymentCharge
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return PaymentCharge
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set source
     *
     * @param string $source
     *
     * @return PaymentCharge
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return PaymentCharge
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return PaymentCharge
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set stripeChargeId
     *
     * @param string $stripeChargeId
     *
     * @return PaymentCharge
     */
    public function setStripeChargeId($stripeChargeId)
    {
        $this->stripeChargeId = $stripeChargeId;

        return $this;
    }

    /**
     * Get stripeChargeId
     *
     * @return string
     */
    public function getStripeChargeId()
    {
        return $this->stripeChargeId;
    }

    /**
     * Set captured
     *
     * @param boolean $captured
     *
     * @return PaymentCharge
     */
    public function setCaptured($captured)
    {
        $this->captured = $captured;

        return $this;
    }

    /**
     * Get captured
     *
     * @return boolean
     */
    public function getCaptured()
    {
        return $this->captured;
    }

    /**
     * Set paid
     *
     * @param boolean $paid
     *
     * @return PaymentCharge
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid
     *
     * @return boolean
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set stripeResponse
     *
     * @param string $stripeResponse
     *
     * @return PaymentCharge
     */
    public function setStripeResponse($stripeResponse)
    {
        $this->stripeResponse = $stripeResponse;

        return $this;
    }

    /**
     * Get stripeResponse
     *
     * @return string
     */
    public function getStripeResponse()
    {
        return $this->stripeResponse;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return PaymentCharge
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * @var string
     */
    private $customer;


    /**
     * Set customer
     *
     * @param string $customer
     *
     * @return PaymentCharge
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return string
     */
    public function getCustomer()
    {
        return $this->customer;
    }
    /**
     * @var \UserBundle\Entity\UserGoal
     */
    private $userGoal;


    /**
     * Set userGoal
     *
     * @param \AppBundle\Entity\UserGoal $userGoal
     *
     * @return PaymentCharge
     */
    public function setUserGoal(\AppBundle\Entity\UserGoal $userGoal = null)
    {
        $this->userGoal = $userGoal;

        return $this;
    }

    /**
     * Get userGoal
     *
     * @return \AppBundle\Entity\UserGoal
     */
    public function getUserGoal()
    {
        return $this->userGoal;
    }
}
