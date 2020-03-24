<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 29-6-16 14:36
 */

namespace UserBundle\Repository;


use UserBundle\Entity\User;
use UserBundle\Repository\Filter\UserFilter;

class UserRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param string|integer $identifier
     *
     * @return null|User
     */
    public function findBySomeIdentifier($identifier)
    {
        if ($result = $this->find($identifier)) {
            return $result;
        }

        if ($result = $this->findOneBy(['email' => $identifier])) {
            return $result;
        }

        if ($result = $this->findOneBy(['username' => $identifier])) {
            return $result;
        }
    }

    /**
     * @param UserFilter $filter
     *
     * @return User[]
     */
    public function findByFilter(UserFilter $filter)
    {
        $qb = $this->createQueryBuilder('u');

        $filter->applyToQuery($qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $role
     *
     * @return User[]
     */
    public function findByRole($role)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
          ->from($this->_entityName, 'u')
          ->where('u.roles LIKE :roles')
          ->setParameter('roles', '%"' . $role . '"%');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return mixed
     */
    public function count()
    {
        return $this->_em->createQuery('SELECT COUNT(u.id) FROM UserBundle\Entity\User u')
          ->getSingleScalarResult();
    }

}