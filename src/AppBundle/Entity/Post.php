<?php

namespace AppBundle\Entity;

use AppBundle\Traits\ForceUpdateTrait;
use AppBundle\Utils\ImageInterface;
use AppBundle\Utils\ImageUploadInterface;
use AppBundle\Utils\UploadPathInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Post
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="post")
 * @ORM\HasLifecycleCallbacks()
 */

class Post implements ImageUploadInterface, ImageInterface
{
    use ForceUpdateTrait;

    const TITLE_PREVIEW_LENGTH = 120;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @Assert\File(mimeTypes={"image/jpg", "image/png", "image/jpeg"}, maxSize = "5M")
     */
    private $image;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $imageFile;

    private $imageName;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $text;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;


    /**
     * @ORM\Column(type="integer", nullable=true)
     * Private field for starting persist\update
     */
    private $fire;

    /**
     * Removed for some times
     * One Product has One Shipment.
     * ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", inversedBy="posts")
     * ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
//    private $category;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * @return Post
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     * @return Post
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }


    /**
     * @ORM\PrePersist()
     */
    public function preCreate()
    {
        $this->setCreated(new \DateTime)
            ->setUpdated(new \DateTime);
    }

    /**
     * @ORM\PreUpdate()
     */

    public function preUpdate()
    {
        $this->setUpdated(new \DateTime());
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     * @return Post
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     * @return Post
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

//    /**
//     * @return mixed
//     */
//    public function getCategory()
//    {
//        return $this->category;
//    }
//
//    /**
//     * @param mixed $category
//     * @return Post
//     */
//    public function setCategory($category)
//    {
//        $this->category = $category;
//        return $this;
//    }



    /**
     * @return mixed
     */
    public function getFire()
    {
        return $this->fire;
    }

    /**
     * @param mixed $fire
     * @return Post
     */
    public function setFire($fire)
    {
        $this->fire = $fire;
        return $this;
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->oldEntity = clone $entity;
        $this->imageName = $this->imageFile;
    }

    public function getImageName()
    {
        if ($this->imageFile instanceof UploadedFile) {
            return $this->imageName;
        }
        return $this->imageFile;
    }

    /**
     * @return mixed
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param mixed $imageFile
     * @return Post
     */
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;
        return $this;
    }

    public function hasImage()
    {
        return $this->getImageFile() != null;
    }

    public function getTitleText()
    {
        $maxLength = self::TITLE_PREVIEW_LENGTH - 3;

        return mb_strlen($this->getTitle()) >= $maxLength  ?
            mb_substr($this->getTitle(), 0, $maxLength) . '...' : $this->getTitle();
    }
}