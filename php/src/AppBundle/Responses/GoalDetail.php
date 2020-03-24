<?php

namespace AppBundle\Responses;

use ApiBundle\Responses\AbstractResponse;
use AppBundle\Entity\Goal;
use AppBundle\Entity\Milestone;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\Request;

class GoalDetail extends AbstractResponse
{
	/**
	 * @var array
	 * @Type("array")
	 * @Groups({"list","details"})
	 */
	protected $data;

	/**
	 * GoalSummary constructor.
	 * @param Goal $goal
	 * @param array $milestones
	 * @param string $basePath
	 */
	public function __construct(Goal $goal, $basePath, $milestones = null)
	{
		$basePath = str_replace('/data/uploads', '/assets/images/rocket-ship.png', $basePath);
		$basePath = rtrim($basePath, '/');
		parent::__construct(200);
		$this->data['goal'] = [
			'id'                     => $goal->getId(),
			'title'                  => $goal->getTitle(),
			'duration'               => $goal->getDuration() . ' ' . $goal->getDurationUnit() . ($goal->getDuration() > 1 ? 's' : ''),
			'description'            => strip_tags($goal->getDescription()),
			'additional_description' => $goal->getAdditionalDescription(),
			'icon'                   => $basePath,//$goal->getIcon(),
			'custom_goal'            => $goal->getDeadline() ? true : false,
			'start_date'             => $goal->getStartDate(),
			'milestones'             => []
		];

		if ($milestones) {
			foreach ($milestones as $milestone) {
				$this->data['goal']['milestones'][] = new MilestoneSummary($milestone);
			}
		}

	}
}