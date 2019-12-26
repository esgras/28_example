<?php

namespace AppBundle\Entity;

use AppBundle\Traits\ForceUpdateTrait;
use AppBundle\Utils\UploadPathInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Utils\ImageInterface;
use AppBundle\Utils\ImageUploadInterface;

/**
 * Class City
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CityRepository")
 * @ORM\Table(name="company_page")
 * @ORM\HasLifecycleCallbacks()
 */

class CompanyPage  implements ImageUploadInterface, ImageInterface
{

    use ForceUpdateTrait;

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
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="string")
     */
    private $slug;

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

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $youtubeLink;

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
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     * @return CompanyPage
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
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
     * @return City
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
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
     * @return CompanyPage
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
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
     * @return CompanyPage
     */
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;
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
     * @return CompanyPage
     */
    public function setImage($image)
    {
        $this->image = $image;
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
     * @return CompanyPage
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

    /**
     * @return mixed
     */
    public function getYoutubeLink()
    {
        return $this->youtubeLink;
    }

    /**
     * @param mixed $youtubeLink
     * @return CompanyPage
     */
    public function setYoutubeLink($youtubeLink)
    {
        $this->youtubeLink = $youtubeLink;
        return $this;
    }


}