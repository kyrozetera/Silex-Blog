<?php
namespace Blog\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class BlogUser implements UserInterface, EquatableInterface
{
    private $username;
    private $password;
    private $salt;
    private $roles;
    private $id;

    /**
     * instantiate BlogUser
     * @param array $data array of user fields
     */
    public function __construct($data)
    {
        $this->setData($data);
    }
    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setData($data)
    {
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->roles = explode(',',$data['roles']);
        $this->id = $data['id'];
    }
    
    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            return false;
        }
        
        if ($this->id !== $user->getId()) {
            return false;
        }
        
        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}
