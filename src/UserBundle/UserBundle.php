<?php

namespace UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use UserBundle\DependencyInjection\UserExtension;

class UserBundle extends Bundle
{
    /**
     * @var string
     */
    private $parent;

    public function __construct($parent = '')
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getContainerExtension()
    {
        return new UserExtension();
    }
}
