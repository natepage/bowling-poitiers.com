<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class PageController extends CRUDController
{
    protected function preCreate(Request $request, $page)
    {
        $page->setAuthor($this->getUser());
    }

    protected function preEdit(Request $request, $page)
    {
        $this->admin->handleOldElements($page, array('pdfs'));
    }
}
