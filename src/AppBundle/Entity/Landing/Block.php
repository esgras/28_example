<?php

namespace AppBundle\Entity\Landing;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class LandingPage
 * @ORM\Entity
 * @ORM\Table(name="landing_block")
 * @ORM\HasLifecycleCallbacks()
 */


class Block
{
    const STATUS_HIDDEN = 0;
    const STATUS_VISIBLE = 1;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $data;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Landing\LandingPage", inversedBy="blocks")
     * @ORM\JoinColumn(nullable=false, name="landing_page_id", referencedColumnName="id", onDelete="cascade")
     */
    private $landingPage;

    /**
     * @ORM\Column(type="integer", options={"default"=1})
     */
    private $status;

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
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     * @return Block
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return Block
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLandingPage()
    {
        return $this->landingPage;
    }

    /**
     * @param mixed $landingPage
     * @return Block
     */
    public function setLandingPage($landingPage)
    {
        $this->landingPage = $landingPage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     * @return Block
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return Block
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getIsVisible()
    {
        return $this->getStatus() == Block::STATUS_VISIBLE;
    }

}