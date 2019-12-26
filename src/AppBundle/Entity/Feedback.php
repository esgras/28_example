<?php

namespace AppBundle\Entity;

use AppBundle\Helper\YoutubeHelper;
use AppBundle\Traits\ForceUpdateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Utils\ImageInterface;
use AppBundle\Utils\ImageUploadInterface;
use AppBundle\Utils\UploadPathInterface;

/**
 * Class Feedback
 * @ORM\Entity
 * @ORM\Table(name="feedback")
 * @ORM\HasLifecycleCallbacks()
 */

class Feedback implements ImageUploadInterface, ImageInterface
{
    use ForceUpdateTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(max=50)
     */
    private $authorName;

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(max=250)
     */
    private $text;

    /**
     * @ORM\Column(type="string")
     * @Assert\Regex(pattern="#^http(s)?://(www\.)?youtu(\.be|be\.com)/#")
     */
    private $link;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $linkPreview;
    /**
     * @Assert\File(mimeTypes={"image/jpg", "image/png", "image/jpeg"}, maxSize = "5M")
     */
    private $image;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $imageFile;

    private $imageName;

    private $hardLinkPreview = false;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=0, max=60)
     */
    private $minutes;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=0, max=60)
     */
    private $seconds;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * Private field for starting persist\update
     */
    private $fire;

    /**
     * @ORM\PrePersist()
     */
    public function preCreate()
    {
        $this->setCreated(new \DateTime)
            ->setUpdated(new \DateTime);
        if (!$this->isHardLinkPreview() && !$this->getImage()) {
            $this->setLinkPreview((new YoutubeHelper)->getImagePathForLink($this->getLink()));
            $this->setLinkPreview((new YoutubeHelper)->getImagePathForLink($this->getLink()));
        }
    }

    /**
     * @ORM\PreUpdate()
     */

    public function preUpdate()
    {
        $this->setUpdated(new \DateTime());
        if (!$this->isHardLinkPreview() && !$this->getImage()) {
            $this->setLinkPreview((new YoutubeHelper)->getImagePathForLink($this->getLink()));
        }
    }

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
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     * @return Feedback
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
     * @return Feedback
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param mixed $authorName
     * @return Feedback
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     * @return Feedback
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinutes()
    {
        return $this->minutes;
    }

    /**
     * @param mixed $minutes
     * @return Feedback
     */
    public function setMinutes($minutes)
    {
        $this->minutes = $minutes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * @param mixed $seconds
     * @return Feedback
     */
    public function setSeconds($seconds)
    {
        $this->seconds = $seconds;
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
     * @return Feedback
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function getTimeText()
    {
        return ($this->getMinutes() ?? '0') . ':' . $this->getSeconds();
    }

    /**
     * @return mixed
     */
    public function getLinkPreview()
    {
        return $this->linkPreview;
    }

    /**
     * @param mixed $linkPreview
     * @return Feedback
     */
    public function setLinkPreview($linkPreview)
    {
        $this->linkPreview = $linkPreview;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHardLinkPreview(): bool
    {
        return $this->hardLinkPreview;
    }

    /**
     * @param bool $hardLinkPreview
     * @return Feedback
     */
    public function setHardLinkPreview(bool $hardLinkPreview)
    {
        $this->hardLinkPreview = $hardLinkPreview;
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
     * @param mixed $imageFile
     * @return Feedback
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
     * @return Feedback
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageFile()
    {
        return $this->imageFile;
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
     * @return Post
     */
    public function setFire($fire)
    {
        $this->fire = $fire;
        return $this;
    }

    public function hasLinkPreview()
    {
        return $this->linkPreview != null;
    }

}