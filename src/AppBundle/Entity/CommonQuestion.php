<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CommonQuestion
 * @ORM\Entity
 * @ORM\Table(name="common_question")
 * @ORM\HasLifecycleCallbacks()
 */

class CommonQuestion
{
    const PREVIEW_TEXT_LENGTH = 230;

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
     * @return CommonQuestion
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
     * @return CommonQuestion
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
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
     * @return CommonQuestion
     */
    public function setText($text)
    {
        $this->text = $text;
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
     * @return CommonQuestion
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getPreviewText()
    {
        return $this->showMoreText()  ?
            mb_substr($this->getText(), 0, $this->getMaxLength()) . '...'  : $this->getText();
    }

    public function getLastText()
    {
       return mb_substr($this->getText(), $this->getMaxLength());
    }

    protected function getMaxLength()
    {
        return self::PREVIEW_TEXT_LENGTH;
    }

    public function showMoreText()
    {
        return mb_strlen($this->getText()) >= $this->getMaxLength();
    }

}