<?php

namespace AppBundle\Entity\Landing;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class LandingPage
 * @ORM\Entity
 * @ORM\Table(name="landing_template")
 * @ORM\HasLifecycleCallbacks()
 */


class Template
{
    const DATA_TYPE_TEXT = 1;
    const DATA_TYPE_ARRAY_TEXT = 2;


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $data;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Template
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param mixed $html
     * @return Template
     */
    public function setHtml($html)
    {
        $this->html = $html;
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
     * @return Template
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

}