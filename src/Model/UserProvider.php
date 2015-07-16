<?php
namespace Blog\Model;
    
/* 
 * Custom user provider
 */

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserProvider extends Model implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $stmt = $this->conn->executeQuery('SELECT * FROM users WHERE username = ?', array(strtolower($username)));

        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return new BlogUser($user);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof BlogUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
    
    function getById($id)
    {
        $stmt = $this->conn->executeQuery('SELECT * FROM users WHERE id = ?', array($id));
        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('User ID "%s" does not exist.', $id));
        }
        return new BlogUser($user);
    }
}
