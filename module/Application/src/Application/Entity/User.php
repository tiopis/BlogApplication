<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * Role Constants
     *
     * @var string
     */
    const ROLE_GUEST = 'GUEST';
    const ROLE_MEMBER = 'MEMBER';
    const ROLE_ADMIN = 'ADMIN';

    public static $roleUserEnum = array(
        0 => self::ROLE_ADMIN,
        1 => self::ROLE_MEMBER,
        2 => self::ROLE_GUEST
    );
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $surname;

    /**
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=60)
     */
    protected $password;

    /**
     * @ORM\Column(type="integer", length=10)
     */
    protected $role = 2;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @ORM\Column(type="string")
     */
    protected $token;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="posts")
     */
    protected $posts;

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param field_type $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return the $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param field_type $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return the $surname
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param field_type $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param field_type $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return the $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param field_type $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return the $password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param field_type $role
     */
    public function setRole($role)
    {
        if((null !== $role) && (!in_array($role, self::$roleUserEnum))) {
            throw new \InvalidArgumentException("Invalid ACL role");
        }
        $this->role = array_search($role, self::$roleUserEnum);

        return $this;
    }

    /**
     * @return the $role
     */
    public function getRole()
    {

        return self::$roleUserEnum[$this->role];
    }

    /**
     * @param field_type $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return the $active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param field_type $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return the $token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function exchangeArray($data = array())
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->surname = (isset($data['surname'])) ? $data['surname'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
    }

    public function isAdmin()
    {
        return $this->getRole() === self::ROLE_ADMIN;
    }

}
