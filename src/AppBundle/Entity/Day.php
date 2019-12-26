<?php

namespace AppBundle\Entity;

use AppBundle\Traits\ForceUpdateTrait;
use AppBundle\Utils\ImageInterface;
use AppBundle\Utils\ImageUploadInterface;
use AppBundle\Utils\UploadPathInterface;
use AppBundle\Utils\VideoUploadInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Day
 * @ORM\Entity
 * @UniqueEntity(fields={"number", "product"})
 * @ORM\Table(uniqueConstraints={
    @ORM\UniqueConstraint(name="day_index", columns={"number", "product_id"})
})
 * @ORM\HasLifecycleCallbacks()
 */

class Day implements ImageUploadInterface, ImageInterface, VideoUploadInterface
{

    use ForceUpdateTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=1, max=365)
     */
    private $number;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

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
     * @ORM\Column(type="integer", nullable=true)
     * Private field for starting persist\update
     */
    private $fire;

    #
    /**
     * @Assert\File(mimeTypes={"video/mpeg", "video/mp4", "video/ogg", "video/quicktime", "video/webm", "video/x-ms-wmv", "video/x-flv", "video/3gpp", "video/3gpp2", "application/octet-stream"}, maxSize="250M")
     */
    private $video;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $videoFile;

    private $videoName;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="productDays")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="cascade")
     */
    private $product;

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
     * @return Day
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * @return Day
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     * @return Day
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
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
     * @return Day
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
        $this->videoName = $this->videoFile;
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
     * @return Day
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

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return Day
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    public function getVideoName()
    {
        if ($this->videoFile instanceof UploadedFile) {
            return $this->videoName;
        }
        return $this->videoFile;
    }

    public function setVideoFile($videoFile)
    {
        $this->videoFile = $videoFile;
        return $this;
    }

    public function getVideo()
    {
        return $this->video;
    }

    public function setVideo($video)
    {
        $this->video = $video;
        return $this;
    }

    public function getVideoFile()
    {
        return $this->videoFile;
    }

    public function hasVideo()
    {
        return $this->getVideoFile() != null;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     * @return Day
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }



}