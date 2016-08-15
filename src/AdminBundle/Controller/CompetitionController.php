<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class CompetitionController extends CRUDController
{
    public function preCreate(Request $request, $competition)
    {
        $competition->setAuthor($this->getUser());
    }
}