<?php
namespace Acme\DemoBundle\Facebook;
use Symfony\Component\Security\Core\User\UserInterface;
class FacebookUser
{
    public function __construct($data)
    {
        $this->id = $data->id;
        $this->username = $data->first_name;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return serialize($this);
    }
}