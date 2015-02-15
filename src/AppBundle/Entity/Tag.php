<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Tag
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
     * @ORM\Column(name="tagName", type="string")
     *
     */
    private $tagName;

    /**
     * @var Tag
     * @ORM\ManyToMany(targetEntity="File", mappedBy="tags")
     */
    private $files;

    public function __files_construct(){
        $this->files= new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @var Tag
     * @ORM\ManyToMany(targetEntity="Folder", mappedBy="tags")
     */
    private $folders;

    public function __folders_construct(){
        $this->folders= new \Doctrine\Common\Collections\ArrayCollection();
    }
    /**
     * @return Tag
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param Tag $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
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
     * @return string
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * @param string $tagName
     */
    public function setTagName($tagName)
    {
        $this->tagName = $tagName;
    }

    /**
     * To String
     *
     * @return string
     */
    function __toString()
    {
        return $this->getTagName();
    }
}
