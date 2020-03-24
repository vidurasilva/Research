<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 29-6-16 10:15
 */

namespace UserBundle\Service;


use Doctrine\ORM\EntityManager;
use NotificationBundle\Services\SegmentationSynchronisationService;
use UserBundle\Entity\User;

class PreferencesService
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;
	/**
	 * @var SegmentationSynchronisationService
	 */
	private $segmentationSynchronisationService;

	/**
	 * PreferencesService constructor.
	 * @param EntityManager $entityManager
	 * @param SegmentationSynchronisationService $segmentationSynchronisationService
	 */
	public function __construct(EntityManager $entityManager, SegmentationSynchronisationService $segmentationSynchronisationService)
	{
		$this->entityManager = $entityManager;
		$this->segmentationSynchronisationService = $segmentationSynchronisationService;
	}

	/**
	 * Overwrite a user their entire set of preferences.
	 *
	 * @param User $user
	 * @param array $preferences
	 */
	public function updatePreferences(User $user, array $preferences)
	{
		foreach ($this->findObsoletePreferences($user, $preferences) as $obsoletePreference) {
			$this->removePreference($obsoletePreference);
		}

		$teamRepo = $this->entityManager->getRepository('TeamBundle:Team');

		foreach ($preferences as $teamId => $preference) {
			$team = $teamRepo->find($teamId);
			if (!$team) {
				//	Something went wrong, so roll back.
				$this->entityManager->rollback();
				throw new \InvalidArgumentException('Team ' . $teamId . ' does not exist');
			}

			$this->updateOrAddPreference($user, $team, $preference);
		}

		$this->segmentationSynchronisationService->queueSynchronisation($user);

		$this->entityManager->flush();
	}

	/**
	 * Save a user their preferences for a specific team.
	 *
	 * @param User $user
	 * @param Team $team
	 * @param array $preference
	 */
	public function saveSinglePreference(User $user, Team $team, array $preference)
	{
		$this->updateOrAddPreference($user, $team, $preference);
		$this->segmentationSynchronisationService->queueSynchronisation($user);
		$this->entityManager->flush();
	}

	/**
	 * Find the preferences that belong to a user+team combination.
	 *
	 * @param User $user
	 * @param Team $team
	 * @return null|TeamPreference
	 */
	public function findPreferenceForTeam(User $user, Team $team)
	{
		/** @var TeamPreference $preference */
		foreach ($user->getTeamPreferences() as $preference) {
			if ($preference->getTeam()->getId() == $team->getId()) {
				return $preference;
			}
		}

		return null;
	}

	/**
	 * Find team preferences that are no longer needed.
	 *
	 * @param User $user
	 * @param array $preferences
	 * @return TeamPreference[]
	 */
	protected function findObsoletePreferences(User $user, array $preferences)
	{
		$obsolete = [];

		/** @var TeamPreference $preference */
		foreach ($user->getTeamPreferences() as $preference) {
			if (!isset($preferences[$preference->getId()])) {
				$obsolete[] = $preference;
			}
		}

		return $obsolete;
	}

	/**
	 * Remove a team preference.
	 *
	 * @param TeamPreference $preference
	 */
	public function removePreference(TeamPreference $preference)
	{
		$this->entityManager->remove($preference);
		$this->entityManager->flush();
	}

	/**
	 * @param User $user
	 * @param Team $team
	 * @param array $preference
	 */
	protected function updateOrAddPreference(User $user, Team $team, array $preference)
	{
		if (!$preferenceEntity = $this->findPreferenceForTeam($user, $team)) {
			$preferenceEntity = new TeamPreference($user, $team);
		}

		$preferenceEntity->setGoalStream((boolean)$preference['goal_stream']);
		$preferenceEntity->setGoalPush((boolean)$preference['goal_push']);
		$preferenceEntity->setLiveStream((boolean)$preference['live_stream']);
		$preferenceEntity->setLivePush((boolean)$preference['live_push']);
		$preferenceEntity->setSummaryStream((boolean)$preference['summary_stream']);
		$preferenceEntity->setSummaryPush((boolean)$preference['summary_push']);
		$preferenceEntity->setMember((boolean)$preference['member']);

		$this->entityManager->persist($preferenceEntity);
	}
}