<?php

namespace SlidesLive\SlidesLiveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SlidesLive\SlidesLiveBundle\Entity\AccountBackup
 *
 * @ORM\Table(name="account_backup")
 * @ORM\Entity
 */
class AccountBackup
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $accountId
     *
     * @ORM\Column(name="accountId", type="integer")
     */
    private $accountId;

    /**
     * @var string $oldPassword
     *
     * @ORM\Column(name="oldPassword", type="string", length=255)
     */
    private $oldPassword;

    /**
     * @var string $oldSalt
     *
     * @ORM\Column(name="oldSalt", type="string", length=255)
     */
    private $oldSalt;

    /**
     * @var string $newPassword
     *
     * @ORM\Column(name="newPassword", type="string", length=255)
     */
    private $newPassword;

    /**
     * @var string $newSalt
     *
     * @ORM\Column(name="newSalt", type="string", length=255)
     */
    private $newSalt;

    /**
     * @var datetime $time
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var string $user
     *
     * @ORM\Column(name="user", type="string", length=255)
     */
    private $user;

    /**
     * @var string $action
     *
     * @ORM\Column(name="action", type="string", length=255)
     */
    private $action;


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
     * Set accountId
     *
     * @param integer $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * Get accountId
     *
     * @return integer 
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set oldPassword
     *
     * @param string $oldPassword
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * Get oldPassword
     *
     * @return string 
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * Set oldSalt
     *
     * @param string $oldSalt
     */
    public function setOldSalt($oldSalt)
    {
        $this->oldSalt = $oldSalt;
    }

    /**
     * Get oldSalt
     *
     * @return string 
     */
    public function getOldSalt()
    {
        return $this->oldSalt;
    }

    /**
     * Set newPassword
     *
     * @param string $newPassword
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    /**
     * Get newPassword
     *
     * @return string 
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * Set newSalt
     *
     * @param string $newSalt
     */
    public function setNewSalt($newSalt)
    {
        $this->newSalt = $newSalt;
    }

    /**
     * Get newSalt
     *
     * @return string 
     */
    public function getNewSalt()
    {
        return $this->newSalt;
    }

    /**
     * Set time
     *
     * @param datetime $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * Get time
     *
     * @return datetime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set user
     *
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set action
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }
}