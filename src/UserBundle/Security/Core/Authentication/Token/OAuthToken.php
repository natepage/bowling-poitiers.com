<?php

namespace UserBundle\Security\Core\Authentication\Token;

use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken as BaseOAuthToken;

class OAuthToken extends BaseOAuthToken
{
    /**
     * @var string
     */
    protected $providerKey;

    /**
     * @param string|array $accessToken The OAuth access token
     * @param array        $roles       Roles for the token
     * @param null|string  $providerKey Token's provider key
     */
    public function __construct($accessToken, array $roles = array(), $providerKey = null)
    {
        parent::__construct($accessToken, $roles);

        $this->providerKey = $providerKey;
    }

    /**
     * Get provider key
     *
     * @return null|string
     */
    public function getProviderKey()
    {
        return $this->providerKey;
    }
}