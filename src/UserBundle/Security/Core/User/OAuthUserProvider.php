<?php

namespace UserBundle\Security\Core\User;

use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuthUserProvider implements OAuthAwareUserProviderInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Loads the user by a given UserResponseInterface object.
     *
     * @param UserResponseInterface $response
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     * @throws \RuntimeException if user class has not setter for resource owner property
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $email = $response->getEmail();

        if(null === $user = $this->userManager->findUserByEmail($email)){
            throw new UsernameNotFoundException(sprintf('User with email [%s] not found.', $email));
        }

        $resourceOwnerName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($resourceOwnerName) . 'Id';

        if(!method_exists($user, $setter)){
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        $username = $response->getUsername();
        $user->$setter($username);

        $this->userManager->updateUser($user);

        return $user;
    }
}