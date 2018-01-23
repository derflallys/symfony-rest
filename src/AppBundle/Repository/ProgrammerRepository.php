<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\Programmer;

class ProgrammerRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return Programmer[]
     */
    public function findAllForUser(User $user)
    {
        return $this->findBy(array('user' => $user));
    }

    /**
     * @param $nickname
     * @return
     */
    public function findOneByNickname($nickname)
    {
        return $this->findOneBy(array('nickname' => $nickname));
    }


    /**
     * @return Programmer
     */
    public function findAny()
    {
        return $this->createQueryBuilder('programmer')
                    ->setMaxResults(1)
                    ->getQuery()->getOneOrNullResult();
    }


    public function findAllQueryBuilder($filter = '')
    {

        $qb =  $this->createQueryBuilder('programmer');

        if($filter)
        {
            $qb->andWhere('programmer.nickname LIKE :filter OR programmer.tagLine LIKE :filter')
                ->setParameter('filter','%'.$filter.'%');
        }

        return $qb;
    }
}
