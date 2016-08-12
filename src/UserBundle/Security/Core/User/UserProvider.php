<?php

namespace UserBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider extends BaseFOSUBProvider
{
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        dump($response);
    }
}