<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Folder
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\FolderRepository")
 */
class Folder
{
      /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="folderName", type="string", length=255)
     */
    private $folderName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="uploadDate", type="datetime")
     */
    private $uploadDate;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="folders")
     */
    private $user;

    /**
     * @var Tag
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="files")
     */
    private $tags;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="sharedFolders")
     */
    private $usersWithAccess;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="File", mappedBy="folder")
     */
    private $files;

    /**
     * @Gedmo\Slug(fields={"folderName"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * Construct
     */
    public function __construct(){
        $this->tags= new \Doctrine\Common\Collections\ArrayCollection();
        $this->usersWithAccess= new \Doctrine\Common\Collections\ArrayCollection();
        $this->files= new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set folderName
     *
     * @param string $folderName
     * @return Folder
     */
    public function setFolderName($folderName)
    {
        $this->folderName = $folderName;

        return $this;
    }

    /**
     * Get folderName
     *
     * @return string 
     */
    public function getFolderName()
    {
        return $this->folderName;
    }

    /**
     * Set uploadDate
     *
     * @param \DateTime $uploadDate
     * @return Folder
     */
    public function setUploadDate($uploadDate)
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    /**
     * Get uploadDate
     *
     * @return \DateTime 
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Folder
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Tag
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * To String
     *
     * @return string
     */
    function __toString()
    {
        return $this->getFolderName();
    }

    /**
     * @return mixed
     */
    public function getUsersWithAccess()
    {
        return $this->usersWithAccess;
    }

    /**
     * @param mixed $usersWithAccess
     */
    public function setUsersWithAccess($usersWithAccess)
    {
        $this->usersWithAccess = $usersWithAccess;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Add tags
     *
     * @param \AppBundle\Entity\Tag $tags
     * @return Folder
     */
    public function addTag(\AppBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \AppBundle\Entity\Tag $tags
     */
    public function removeTag(\AppBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Add usersWithAccess
     *
     * @param \AppBundle\Entity\User $usersWithAccess
     * @return Folder
     */
    public function addUsersWithAccess(\AppBundle\Entity\User $usersWithAccess)
    {
        $this->usersWithAccess[] = $usersWithAccess;

        return $this;
    }

    /**
     * Remove usersWithAccess
     *
     * @param \AppBundle\Entity\User $usersWithAccess
     */
    public function removeUsersWithAccess(\AppBundle\Entity\User $usersWithAccess)
    {
        $this->usersWithAccess->removeElement($usersWithAccess);
    }

    /**
     * Add files
     *
     * @param \AppBundle\Entity\File $files
     * @return Folder
     */
    public function addFile(\AppBundle\Entity\File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \AppBundle\Entity\File $files
     */
    public function removeFile(\AppBundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }
}