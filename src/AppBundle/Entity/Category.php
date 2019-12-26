<?php

namespace AppBundle\Entity;

use AppBundle\Utils\UploadPathInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Category
 * @ORM\Table(name="category")
 * @ORM\HasLifecycleCallbacks()
 */

class Category
{
    const UPLOAD_PATH = 'upload/category';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="category", cascade={"remove"})
     */
    private $posts;

    /**
     * Private field for starting persist\update
     */
    private $fire;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Page
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return Page
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param mixed $posts
     * @return Category
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }


    public function getUploadPath()
    {
        return self::UPLOAD_PATH;
    }

    /**
     * @return mixed
     */
    public function getFire()
    {
        return $this->fire;
    }

    /**
     * @param mixed $fire
     * @return Category
     */
    public function setFire($fire)
    {
        $this->fire = $fire;
        return $this;
    }

}