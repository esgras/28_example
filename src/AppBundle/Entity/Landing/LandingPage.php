<?php

namespace AppBundle\Entity\Landing;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LandingPage
 * @ORM\Entity
 * @ORM\Table(name="landing_page")
 * @ORM\HasLifecycleCallbacks()
 */

class LandingPage
{
    const STATUS_DRAFT = 0;
    const STATUS_VISIBLE = 1;

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
     * @ORM\Column(type="string", nullable=true)
     */
    private $additionalCss;

    /**
     * @Assert\File(mimeTypes={"text/css", "text/plain"}, maxSize = "5M")
     */
    private $cssFile;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Landing\Block", mappedBy="landingPage", cascade={"remove"})
     */
    private $blocks;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="string")
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

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

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
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
     * @return LandingPage
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
     * @return LandingPage
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
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
     * @return LandingPage
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdditionalCss()
    {
        return $this->additionalCss;
    }

    /**
     * @param mixed $additionalCss
     * @return LandingPage
     */
    public function setAdditionalCss($additionalCss)
    {
        $this->additionalCss = $additionalCss;
        return $this;
    }

//    /**
//     * @return mixed
//     */
//    public function getHead()
//    {
//        return $this->head;
//    }
//
//    /**
//     * @param mixed $head
//     * @return LandingPage
//     */
//    public function setHead($head)
//    {
//        $this->head = $head;
//        return $this;
//    }



    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return LandingPage
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function isVisible()
    {
        return $this->getStatus() == self::STATUS_VISIBLE;
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
     * @return LandingPage
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param mixed $blocks
     * @return LandingPage
     */
    public function setBlocks($blocks)
    {
        $this->blocks = $blocks;
        return $this;
    }


    public function addBlock(Block $block)
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks->add($block);
            $block->setLandingPage($this);
        }
    }

    public function getBlockByPosition($position)
    {
        $blocks = $this->getBlocks();
        if (!$blocks) return null;

        foreach($blocks as $block) {
            if ($position == $block->getPosition()) {
                return $block;
            }
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getCssFile()
    {
        return $this->cssFile;
    }

    /**
     * @param mixed $cssFile
     * @return LandingPage
     */
    public function setCssFile($cssFile)
    {
        $this->cssFile = $cssFile;
        return $this;
    }



}