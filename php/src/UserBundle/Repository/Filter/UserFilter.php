<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 11-7-16 9:10
 */

namespace UserBundle\Repository\Filter;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

class UserFilter
{
	protected $roles;

	/**
	 * UserFilter constructor.
	 */
	public function __construct()
	{
		$this->roles = [];
	}

	public function role($role)
	{
		$this->roles[] = $role;
	}
	
	public function applyToQuery(QueryBuilder $qb)
	{
		if ($this->roles) {
			$or = $qb->expr()->orX();

			foreach ($this->roles as $role) {
				$or->add(
					$qb->expr()->like('u.roles', $qb->expr()->literal('%"' . $role . '"%'))
				);
			}

			$qb->andWhere($or);
		}
	}
}