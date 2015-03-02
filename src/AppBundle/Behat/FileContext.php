<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 11/01/15
 * Time: 19:14
 */

namespace AppBundle\Behat;


use AppBundle\Entity\File;
use AppBundle\Entity\Folder;
use Behat\Gherkin\Node\TableNode;

class FileContext extends CoreContext
{
    /**
     * @Given existing files:
     */
    public function createFileList (TableNode $tableNode)
    {
        $em = $this->getEntityManager();
        foreach ($tableNode as $hash){

            $file = new File();

            $user=$this->getEntityManager()->getRepository('AppBundle:User')->findOneByUsername($hash['username']);
            $userWithAccess=$this->getEntityManager()->getRepository('AppBundle:User')->findOneByUsername($hash['userWithAccess']);
            $folder=$this->getEntityManager()->getRepository('AppBundle:Folder')->findOneByFolderName($hash['folder']);
            $tag=$this->getEntityManager()->getRepository('AppBundle:Tag')->findOneByTagName($hash['tags']);
            $file->setFilename($hash['filename']);
            $file->setUploadDate(new \DateTime($hash['uploadDate']));
            $file->setPassword('secret');
            $file->setUser($user);
            $file->setFolder($folder);
            if($userWithAccess) $file->addUsersWithAccess($userWithAccess);
            if($tag) $file->addTag($tag);

            $em-> persist($file);
        }
        $em->flush();
    }

    /**
     * @Given existing folders:
     */
    public function createFolderList(TableNode $tableNode)
    {
        $em = $this->getEntityManager();
        foreach ($tableNode as $hash) {

            $folder = new Folder();

            $user=$this->getEntityManager()->getRepository('AppBundle:User')->findOneByUsername($hash['username']);
            $userWithAccess=$this->getEntityManager()->getRepository('AppBundle:User')->findOneByUsername($hash['userWithAccess']);
            $tag=$this->getEntityManager()->getRepository('AppBundle:Tag')->findOneByTagName($hash['tags']);
            $folder->setFolderName($hash['folderName']);
            $folder->setDescription($hash['description']);
            $folder->setUploadDate(new \DateTime($hash['uploadDate']));
            $folder->setUser($user);
            if($userWithAccess) $folder->addUsersWithAccess($userWithAccess);
            if($tag) $folder->addTag($tag);

            $em->persist($folder);
        }
        $em->flush();
    }

    /**
     * @Then /^I should see (\d+) elements/
     */
    public function iShouldSeeFiles( $numElements )
    {
        $this->assertSession()->elementsCount('css', '.info', $numElements);
    }

    /**
     * @Given :username has access to :folderName
     */
    public function hasAccess($username,$folderName){

        $user=$this->getEntityManager()->getRepository('AppBundle:User')->findOneByUsername($username);
        $folder=$this->getEntityManager()->getRepository('AppBundle:Folder')->findOneByFolderName($folderName);

        if ($folder->getUser()==$user)
            return true;
        foreach ($folder->usersWithAccess as $uWithAccess){
            if($user==$uWithAccess)
                return true;
        }
        return false;
    }

    /**
     * @Then access is granted to :username in :folderName
     */

    public function grantAccessToFolder($username,$folderName){

        $user=$this->getEntityManager()->getRepository('AppBundle:User')->findOneByUsername($username);
        $folder=$this->getEntityManager()->getRepository('AppBundle:Folder')->findOneByFolderName($folderName);

        $folder->addUsersWithAccess($user);
    }
}