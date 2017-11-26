<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\PostRepository")
 * @ORM\Table(name="posts")
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string")
     */
    protected $text;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $image;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    protected $userId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $dateInsert;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $dateUpdate;

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param field_type $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param field_type $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return the $text
     */
    public function getText()
    {
        return $this->text;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }
    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * @param field_type $text
     */
    public function setDateInsert($dateInsert)
    {
        $this->dateInsert = $dateInsert;
    }

     /**
     * @return the $text
     */
    public function getDateInsert()
    {
        return $this->dateInsert;
    }

    /**
     * @param field_type $text
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;
    }

    /**
     * @return the $text
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * @param field_type $text
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return the $text
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setDateUpdate(new \Datetime());
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setDateInsert(new \Datetime());
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
        $this->title = (isset($data['title'])) ? $data['title'] : null;
        $this->text = (isset($data['text'])) ? $data['text'] : null;
        $this->image = (isset($data['image'])) ? $data['image'] : null;
        $this->dateInsert = (isset($data['dateInsert'])) ? $data['dateInsert'] : null;
        $this->dateUpdate = (isset($data['dateUpdate'])) ? $data['dateUpdate'] : null;
    }

    public function canBeChanged(User $user)
    {
        return $user->getRole() === User::ROLE_ADMIN ||
            $user->getId() === $this->getUser()->getId();
    }
}
