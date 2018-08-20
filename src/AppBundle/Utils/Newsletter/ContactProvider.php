<?php

namespace AppBundle\Utils\Newsletter;

use Doctrine\Common\Persistence\ObjectManager;

class ContactProvider implements ContactProviderInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var boolean
     */
    private $isSuperAdmin;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->isSuperAdmin = false;
    }

    public function setIsSuperAdmin($isSuperAdmin)
    {
        $this->isSuperAdmin = $isSuperAdmin;
    }

    public function getContacts()
    {
        $contacts = array();
        $emails = array();

        /**
         * @var \UserBundle\Entity\User $user
         */
        foreach($this->getUsers() as $user){
            if(!\in_array($email = $user->getEmail(), $emails, true)){
                $emails[] = $email;

                $contact = new Contact();
                $contact->setEmail($email)
                        ->setUsername($user->getUsername())
                        ->setType(ContactInterface::TYPE_USER);

                $contacts[] = $contact;
            }
        }

        /**
         * @var \AppBundle\Entity\Newsletter $newsletter
         */
        foreach($this->getNewsletters() as $newsletter){
            if(!\in_array($email = $newsletter->getMail(), $emails, true)){
                // If not activated, skip
                if ($newsletter->isActivated() === false && (((int)\date('Ymd')) >= 20180901)) { // Remove that later
                    continue;
                }

                $emails[] = $email;

                $contact = new Contact();
                $contact->setEmail($email)
                        ->setToken($newsletter->getToken())
                        ->setUnSubscribable(true)
                        ->setType(ContactInterface::TYPE_NEWSLETTER);

                $contacts[] = $contact;
            }
        }

        return $contacts;
    }

    public function getContactsFormRepresentation()
    {
        $formRepresentation = array();

        foreach($this->getContacts() as $contact){
            if(!array_key_exists($email = $contact->getEmail(), $formRepresentation)) {
                $render = \sprintf(
                    '[%s] %s%s',
                    \strtoupper($contact->getType()),
                    $contact->getUsername() !== null ? $contact->getUsername() . ' - ' : '',
                    $email
                );

                $formRepresentation[$render] = $email;
            }
        }

        return $formRepresentation;
    }

    public function getContactsEmail()
    {
        $emails = array();

        foreach($this->getContacts() as $contact){
            if(!\in_array($email = $contact->getEmail(), $emails, true)){
                $emails[] = $email;
            }
        }

        return $emails;
    }

    /**
     * Returns an array with users email.
     *
     * @return array
     */
    public function getUsersEmail()
    {
        $emails = array();

        foreach ($this->getContacts() as $contact) {
            if ($contact->getType() !== ContactInterface::TYPE_USER) {
                continue;
            }

            if(!\in_array($email = $contact->getEmail(), $emails, true)){
                $emails[] = $email;
            }
        }

        return $emails;
    }

    /**
     * Returns an array with newsletter email.
     *
     * @return array
     */
    public function getNewslettersEmail()
    {
        $emails = array();

        foreach ($this->getContacts() as $contact) {
            if ($contact->getType() !== ContactInterface::TYPE_NEWSLETTER) {
                continue;
            }

            if(!\in_array($email = $contact->getEmail(), $emails, true)){
                $emails[] = $email;
            }
        }

        return $emails;
    }

    private function getUsers()
    {
        return $this->om
            ->getRepository('UserBundle:User')
            ->findBy(array('newsletter' => 1));
    }

    private function getNewsletters()
    {
        return $this->om
            ->getRepository('AppBundle:Newsletter')
            ->findAll();
    }
}
