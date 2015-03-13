<?php
/**
 * Created by PhpStorm.
 * User: juanan
 * Date: 30/01/15
 * Time: 20:35
 */


namespace AppBundle\Entity;
use Doctrine\ORM\EntityRepository;


class FileRepository extends EntityRepository
{

    public function findFiles($word)
    {
        $em = $this->getEntityManager();
        $query=$em->createQuery('
            SELECT c,d
            FROM AppBundle:File c
            LEFT JOIN c.tags d
            WHERE c.filename LIKE :word
            OR d.tagName LIKE :word
            ORDER BY c.filename ASC'
        );
        $query->setParameter('word', '%'.$word.'%');
        return $query->getResult();
    }

    public function myFiles($owner)
    {
        $em = $this->getEntityManager();
        $query=$em->createQuery('
            SELECT c
            FROM AppBundle:File c
            WHERE c.user LIKE :owner
            ORDER BY c.filename ASC'
        );
        $query->setParameter('owner', $owner);
        return $query->getResult();
    }

    public function findAllOrderedByName()
    {
        $em=$this->getEntityManager();
        $query=$em->createQuery('
            SELECT c
            FROM AppBundle:File c
            ORDER BY c.filename ASC
        ');
        return $query->getResult();
    }

//    public function findAllSharedWithMe($user)
//    {
//        $em=$this->getEntityManager();
//        $query=$em->createQuery('
//            SELECT c
//            FROM AppBundle:File c
//            WHERE c.usersWithAccess LIKE :user
//            ORDER BY c.filename ASC
//        ');
//
//        $query->setParameter('user', $user);
//        return $query->getResult();
//    }
}



