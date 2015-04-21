<?php

/**
 * Created by PhpStorm.
 * User: juanan
 * Date: 30/01/15
 * Time: 20:35.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class FileRepository extends EntityRepository
{
    public function listFiles()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT c
            FROM AppBundle:File c
            WHERE c.folder IS NULL
            AND c.scanStatus = :status
            ORDER BY c.filename ASC'
        );

        $query->setParameter('status',File::SCAN_STATUS_OK);
        return $query->getResult();
    }

    public function findFiles($word)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT c,d
            FROM AppBundle:File c
            LEFT JOIN c.tags d
            WHERE c.filename LIKE :word
            OR d.tagName LIKE :word
            AND c.scanStatus = :status
            ORDER BY c.filename ASC'
        );
        $query->setParameter('word', '%'.$word.'%');
        $query->setParameter('status',File::SCAN_STATUS_OK);

        return $query->getResult();
    }

    public function myFiles($owner)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT c
            FROM AppBundle:File c
            WHERE c.user = :owner
            AND c.scanStatus = :status
            ORDER BY c.filename ASC'
        );
        $query->setParameter('owner', $owner);
        $query->setParameter('status',File::SCAN_STATUS_OK);

        return $query->getResult();
    }

    public function findSharedFiles($user)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT c,d
            FROM AppBundle:File c
            JOIN c.usersWithAccess d
            WHERE d.username = :user
            AND c.scanStatus = :status
            ORDER BY c.filename ASC'
        );
        $query->setParameter('user', $user->getUsername());
        $query->setParameter('status',File::SCAN_STATUS_OK);

        return $query->getResult();
    }

    public function deleteLapsedFiles($date)
    {
        $em = $this->getEntityManager();
        $files = $em->getRepository('AppBundle:File')->findAll();
        foreach ($files as $file) {
            if ($file->getUploadDate() == $date) {
                $em->remove($file);
                $em->persist($file);
            }
        }
        $em->flush();
    }
}
