<?php

namespace SlidesLive\SlidesLiveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SlidesLive\SlidesLiveBundle\Entity\Subscribe
 *
 * @ORM\Table(name="subscribe")
 * @ORM\Entity
 */
class Subscribe
{
    public function __construct()
    {
        $this->subscribedAt = new \DateTime('now');
    }

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var datetime $subscribedAt
     *
     * @ORM\Column(name="subscribed_at", type="datetime")
     */
    private $subscribedAt;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set subscribedAt
     *
     * @param datetime $subscribedAt
     */
    public function setSubscribedAt($subscribedAt)
    {
        $this->subscribedAt = $subscribedAt;
    }

    /**
     * Get subscribedAt
     *
     * @return datetime
     */
    public function getSubscribedAt()
    {
        return $this->subscribedAt;
    }
}