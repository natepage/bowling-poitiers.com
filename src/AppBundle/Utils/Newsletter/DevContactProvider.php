<?php

namespace AppBundle\Utils\Newsletter;

use Doctrine\Common\Persistence\ObjectManager;

class DevContactProvider extends ContactProvider
{
    /**
     * @var integer
     */
    protected $id;

    public function __construct(ObjectManager $om, $id)
    {
        parent::__construct($om);

        $this->id = $id;
    }

    public function getContacts()
    {
        $user = $this->om->getRepository('UserBundle:User')->find($this->id);

        if(null === $user){
            throw new \InvalidArgumentException(sprintf("User with id[%s] doesn't exist.", $this->id));
        }

        $contact = new Contact();
        $contact->setEmail($user->getEmail())
                ->setUsername($user->getUsername());

        return array($contact);
    }
}