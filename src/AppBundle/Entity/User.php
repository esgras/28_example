<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity("email")
 * @ORM\Table(name="user", uniqueConstraints={
@ORM\UniqueConstraint(name="email_index", columns={"email"})
})
 * @ORM\HasLifecycleCallbacks()
 */

class User implements \Serializable, UserInterface
{
    const MINIMAL_ROLE = 'ROLE_USER';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_WAITING_FOR_STATUS = 1;

    const MALE = 'мужчина';
    const FEMALE = 'женщина';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(min="8", minMessage="This value is too short. Min length is {{ limit }} characters")
     * @Assert\Regex(pattern="/([a-zA-Z]\d|\d[a-zA-Z])/", message="Password should have 1 letter and 1 digit")
     */
    private $password;

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
     */
    private $status;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * Hash for temporary operations
     * @ORM\Column(type="string", nullable=true)
     */
    private $hash;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $aim;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $scope;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $started;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isWaitingForStart;

    /**
     * Many users have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="users")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;


    /**
     * @ORM\Column(type="integer", nullable=true)
     * Number of Day to send Letter
     */
    private $dayNumber;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password
            ) = unserialize($serialized);
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
     * @ORM\PostLoad()
     */

    public function postLoad()
    {
//        $this->aim = $this->aim ?? '';
//        $this->age = $this->age ?? '';
//        $this->scope = $this->scope ?? '';
//        $this->name = $this->name ?? '';
//        $this->city = $this->city ?? '';
//        $this->gender = $this->gender ?? '';
//        $this->position = $this->position ?? '';
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
     * @return User
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
     * @return User
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    public function rolesIsEmpty()
    {
        return empty($this->roles) ||
            $this->roles instanceof ArrayCollection && !$this->roles->count();
    }

    public function getRoles()
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if ($this->rolesIsEmpty()) {
            $roles[] = self::MINIMAL_ROLE;
        }

        return $roles;
    }

    public function getRolesArray()
    {
        return is_array($this->getRoles()) ? $this->getRoles() : $this->getRoles()->toArray();
    }

    public function getRolesText()
    {
        return "";
    }



    /**
     * @param mixed $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
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
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     * @return User
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    public function isSuperAdmin()
    {
        return in_array(self::ROLE_SUPER_ADMIN, $this->getRoles());
    }

    public function notActive()
    {
        return $this->getStatus() == self::STATUS_NOT_ACTIVE;
    }

    public function isAdmin()
    {
        return in_array(self::ROLE_SUPER_ADMIN, $this->getRoles());
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
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     * @return User
     */
    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAim()
    {
        return $this->aim;
    }

    /**
     * @param mixed $aim
     * @return User
     */
    public function setAim($aim)
    {
        $this->aim = $aim;
        return $this;
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
     * @return User
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param mixed $scope
     * @return User
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * @param mixed $started
     * @return User
     */
    public function setStarted($started)
    {
        $this->started = $started;
        return $this;
    }

    public function hasStartedChallenge()
    {
        return $this->started != NULL;
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
     * @return User
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }


    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function getDayNumber()
    {
        return (int) $this->dayNumber;
    }

    /**
     * @param mixed $dayNumber
     * @return User
     */
    public function setDayNumber($dayNumber)
    {
        $this->dayNumber = $dayNumber;
        return $this;
    }

    public function addDay()
    {
        $this->setDayNumber($this->getDayNumber() ? $this->getDayNumber() + 1 : 1);
        return $this;
    }

    public function resetDays()
    {
        $this->setDayNumber(1);
    }

    public function canAddDay()
    {
        $product = $this->getProduct();

        return !$product || $this->checkDaysByProduct($product) ? false : true;
    }

    public function checkTemplatesForProduct()
    {
        $product = $this->getProduct();
        
//        dump($product->getDays()); die;

        return $product && $this->templatesByProduct($product);
    }

    public function templatesByProduct(Product $product)
    {
        return $this->getDayNumber() <= $product->getDays();
    }

    protected function checkDaysByProduct(Product $product)
    {
        return $this->getDayNumber() > $product->getDays();
    }

    public function isNotActive()
    {
        return $this->getStatus() == self::STATUS_NOT_ACTIVE;
    }

    /**
     * @return mixed
     */
    public function getisWaitingForStart()
    {
        return $this->isWaitingForStart;
    }

    /**
     * @param mixed $isWaitingForStart
     * @return User
     */
    public function setIsWaitingForStart($isWaitingForStart)
    {
        $this->isWaitingForStart = $isWaitingForStart;
        return $this;
    }



}