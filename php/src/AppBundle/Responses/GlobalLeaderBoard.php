<?php


namespace AppBundle\Responses;


use ApiBundle\Responses\AbstractResponse;
use AppBundle\Entity\Score;
use AppBundle\ValueObjects\TotalScore;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use UserBundle\Responses\UserSummary;

class GlobalLeaderBoard extends AbstractResponse
{

    /**
     * @var array
     * @Type("array")
     * @Groups({"list","details"})
     */
    protected $data;

    /**
     * GlobalLeaderBoard constructor.
     *
     * @param Score $userScore
     * @param int $userPosition
     * @param array $topTenScore
     * @param string $basePath
     */
    public function __construct(
      Score $userScore,
      $userPosition,
      array $topTenScore = [],
      $basePath
    ) {
        parent::__construct(200);
        $this->data['userScore'] = [
          'user' => new UserSummary($userScore->getUser(), $basePath),
          'score' => $userScore->getScore(),
          'position' => (int)$userPosition,
        ];

        $position = 1;
        $realPosition = 1;
        $prev = 0;

        $this->data['topScore'] = [];

        /** @var TotalScore $topScore */
        foreach ($topTenScore as $topScore) {
            if ($prev !== 0 && $prev !== $topScore->getScore()) {
                $position++;
            }

            if ($prev !== $topScore->getScore()) {
                $position = $realPosition;
            }

            $this->data['topScore'][] = [
              'user' => new UserSummary($topScore->getUser(), $basePath),
              'score' => $topScore->getScore(),
              'position' => (int)$position,
            ];

            $prev = $topScore->getScore();
            $realPosition++;
        }
    }
}