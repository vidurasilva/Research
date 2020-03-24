<?php

namespace AppBundle\Responses;

use AppBundle\Entity\CommunityCategory;
use AppBundle\Entity\Goal;
use AppBundle\Entity\GoalCategory;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use ApiBundle\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Request;

class GoalSummary extends AbstractResponse
{

    protected $status;

    /**
     * @var int
     * @Groups({"list","details"})
     * @Type("integer")
     */
    protected $id;

    /**
     * @var string
     * @Groups({"list","details"})
     * @Type("string")
     */
    protected $title;

    /**
     * @var string
     * @Groups({"list","details"})
     * @Type("string")
     */
    protected $duration;

    /**
     * @var string
     * @Groups({"list","details"})
     * @Type("string")
     */
    protected $description;

    /**
     * @var string
     * @Groups({"list","details"})
     * @Type("string")
     */
    protected $additionalDescription;

    /**
     * @var string
     * @Groups({"list","details"})
     * @Type("string")
     */
    protected $icon;

    /**
     * @var array
     * @Type("array")
     * @Groups({"details"})
     */
    protected $milestones;

    /**
     * @var CommunityCategorySummary
     * @Type("AppBundle\Responses\CommunityCategorySummary")
     * @Groups({"details","list"})
     */
    protected $communityCategory;

    /**
     * @var GoalCategory
     * @Type("AppBundle\Responses\CategorySummary")
     * @Groups({"details","list"})
     */
    protected $category;

    /**
     * @var boolean
     * @Type("boolean")
     * @Groups({"details","list"})
     */
    protected $customGoal = false;

    /**
     * @var int
     * @Groups({"list","details"})
     * @Type("integer")
     */
    protected $originalGoalId;

    /**
     * GoalSummary constructor.
     *
     * @param Goal $goal
     * @param array $milestones
     * @param string $basePath
     */
    public function __construct(Goal $goal, $basePath, array $milestones = null)
    {
        parent::__construct(200);
        $this->id = $goal->getId();
        $this->title = $goal->getTitle();
        $this->description = strip_tags($goal->getDescription());
        $this->additionalDescription = $goal->getAdditionalDescription();
        $this->originalGoalId = !empty($goal->getOriginGoal()) ? $goal->getOriginGoal()->getId() : null;
        if ($goal->getIcon()) {
            $this->icon = $basePath . $goal->getIcon();
        } else {
            $this->icon = str_replace('/data/uploads/goals', '/data/uploads',
              $basePath);
            $this->icon = str_replace('/data/uploads',
              '/assets/images/rocket-ship.png', $this->icon);
            $this->icon = rtrim($this->icon, '/');
        }
        $this->duration = $goal->getDuration() . ' ' . $goal->getDurationUnit() . ($goal->getDuration() > 1 ? 's' : '');
        if ($milestones) {
            $this->milestones = [];
            foreach ($milestones as $milestone) {
                $this->milestones[] = new MilestoneSummary($milestone);
            }
        }

        if ($goal->getCommunityCategory()) {
            $this->communityCategory = new CommunityCategorySummary($goal->getCommunityCategory(),
              str_replace('/data/uploads/goals', '/data/uploads', $basePath));
        }

        if ($goal->getCategory()) {
            $this->category = new CategorySummary($goal->getCategory(),
              $basePath);
        }

        if (null !== $goal->getDeadline()) {
            $this->customGoal = true;
        }

    }
}