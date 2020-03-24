<?php


namespace AppBundle\ValueObjects;


use AppBundle\Entity\Score;
use UserBundle\Entity\User;

class TotalScore
{

	/** @var User */
	private $user;

	/** @var int */
	private $score;

	/**
	 * TotalScore constructor.
	 *
	 * @param Score  $score
	 * @param string $totalScore
	 */
	public function __construct(Score $score, $totalScore)
	{
		$this->setUser($score->getUser());
		$this->setScore($totalScore);
	}

	/**
	 * @param User $user
	 *
	 * @return TotalScore
	 */
	public function setUser($user)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * @return User
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param int $score
	 *
	 * @return TotalScore
	 */
	public function setScore($score)
	{
		$this->score = $score;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getScore()
	{
		return $this->score;
	}


}