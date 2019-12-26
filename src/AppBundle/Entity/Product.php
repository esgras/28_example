<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Subscriber
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 * @ORM\Table(name="product")
 * @ORM\HasLifecycleCallbacks()
 */

class Product
{
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 0;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(max=27)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="integer")
     */
    private $days;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2)
     */
    private $price;

    /**
     * One product has many users. This is the inverse side.
     * @ORM\OneToMany(targetEntity="User", mappedBy="product")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Day", mappedBy="product", cascade={"remove"})
     */
    private $productDays;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    public function __construct()
    {
        $this->productDays = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return Product
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param mixed $days
     * @return Product
     */
    public function setDays($days)
    {
        $this->days = $days;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    public function getPriceWithDiscount($discount=0)
    {
        return (int) $this->price * (100 - $discount) / 100;
    }

    /**
     * @param mixed $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     * @return Product
     */
    public function setUsers($users)
    {
        $this->users = $users;
        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }



    public function getPriceText($discount=0)
    {
        $str = strval(round($this->getPriceWithDiscount($discount)));
        $partLen = 3;
        $end = '';

        do {
            $part = substr($str, -3);
            $str = substr($str, 0, strlen($str) - strlen($part));
            if (strlen($part)) {
                $end = $part . ' ' . $end;
            }
        } while(strlen($str) > $partLen);

        if (strlen($end)) {
            $end = $str . ' ' . $end;
        } else {
            $end = $str;
        }

        return trim($end);
    }

    /**
     * @return mixed
     */
    public function getProductDays()
    {
        return $this->productDays;
    }

    /**
     * @param mixed $productDays
     * @return Product
     */
    public function setProductDays($productDays)
    {
        $this->productDays = $productDays;
        return $this;
    }

    public function addProductDay(Day $day)
    {
        if (!$this->productDays->contains($day)) {
            $this->productDays->add($day);
            $day->setProduct($this);
        }
    }

    public function removeProductDay(Day $day)
    {
        if ($this->productDays->contains($day)) {
            $this->productDays->removeElement($day);
            $day->setProduct(null);
        }
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
     * @return Product
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }




    public function isProductActive()
    {
        return (bool) $this->getStatus();
    }

    /**
     * @return mixed
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param mixed $month
     * @return Product
     */
    public function setMonth($month)
    {
        $this->month = $month;
        return $this;
    }



}