<?php

namespace Meta\MetaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Meta\MetaBundle\Entity\Subscribe
 *
 * @ORM\Table(name="subscribe")
 * @ORM\Entity
 */
class Subscribe
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;
    
    /**
     *@ORM\ManyToOne(targetEntity="Channel", inversedBy="subscribes")
     *@ORM\JoinColumn(name="channel_id", referencedColumnName="id")
     */              
    protected $channel;


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
     * Set channel
     *
     * @param Meta\MetaBundle\Entity\Channel $channel
     */
    public function setChannel(\Meta\MetaBundle\Entity\Channel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Get channel
     *
     * @return Meta\MetaBundle\Entity\Channel 
     */
    public function getChannel()
    {
        return $this->channel;
    }
}