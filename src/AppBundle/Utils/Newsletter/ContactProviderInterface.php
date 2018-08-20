<?php

namespace AppBundle\Utils\Newsletter;

interface ContactProviderInterface
{
    /**
     * Returns an array with contacts instances.
     * 
     * @return array
     */
    public function getContacts();

    /**
     * Returns an array with contacts form representation
     * 
     * @return array
     */
    public function getContactsFormRepresentation();

    /**
     * Returns an array with contacts email
     * 
     * @return array
     */
    public function getContactsEmail();

    /**
     * Returns an array with users email.
     *
     * @return array
     */
    public function getUsersEmail();

    /**
     * Returns an array with newsletter email.
     *
     * @return array
     */
    public function getNewslettersEmail();
}
