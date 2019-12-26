<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Subscriber
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="`order`")
 * @ORM\HasLifecycleCallbacks()
 */

class Order {

    const STATUS_NEW = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_BAD = 2;
    const STATUS_OLD = 3;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * Inner Code For Payment
     */
    private $code;

    /**
     * @ORM\Column(type="string", nullable=true)
     * Code From Payment System
     */
    private $orderId;

    /**
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2)
     */
    private $total;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $withDiscount;



    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     * @return Order
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return Order
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     * @return Order
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
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
     * @return Order
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function preCreate()
    {
        $this->setCreated(new \DateTime);
    }

    public function makeCode()
    {
        $this->code = uniqid();

        return $this;
    }

    public function getPriceForPayment()
    {
        return (int)((float)$this->getTotal() * 100);
    }

    public function getPriceForPaymentWithDiscount($discount = 0)
    {
        return (int) $this->getPriceForPayment() * (100 - $discount) / 100;
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
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     * @return Order
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }



    public function isNew()
    {
        return $this->getStatus() == self::STATUS_NEW;
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
     * @return Order
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return Order
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWithDiscount()
    {
        return $this->withDiscount;
    }

    /**
     * @param mixed $withDiscount
     * @return Order
     */
    public function setWithDiscount($withDiscount)
    {
        $this->withDiscount = $withDiscount;
        return $this;
    }

    public function clearUser()
    {
        $this->setUser(NULL);
        return $this;
    }

}