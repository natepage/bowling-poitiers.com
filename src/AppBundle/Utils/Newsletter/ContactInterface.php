<?php

namespace AppBundle\Utils\Newsletter;

interface ContactInterface
{
    const TYPE_USER = 'user';
    const TYPE_NEWSLETTER = 'newsletter';

    /**
     * Sets the username.
     *
     * @param string $username
     * @return self
     */
    public function setUsername($username);

    /**
     * Gets the username.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Sets the email.
     *
     * @param string $email
     * @return self
     */
    public function setEmail($email);

    /**
     * Gets the email.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Sets the token.
     *
     * @param string $token
     * @return self
     */
    public function setToken($token);

    /**
     * Gets the token.
     *
     * @return string
     */
    public function getToken();

    /**
     * Tells if the contact can unsubscribe.
     *
     * @param boolean $unSubscribable
     * @return self
     */
    public function setUnSubscribable($unSubscribable);

    /**
     * Check if the contact can unsubscribe.
     * 
     * @return boolean
     */
    public function isUnSubscribable();

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return self
     */
    public function setType($type);

    /**
     * Get type.
     *
     * @return string
     */
    public function getType();
}
