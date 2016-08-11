<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends Controller
{
    /**
     * @Route("/admin/cookie/{cookieName}", name="admin_cookie")
     * @Method("GET")
     */
    public function cookie($cookieName = null)
    {
        if(null === $cookieName || !is_scalar($cookieName)) {
            throw $this->createNotFoundException();
        }

        $cookie = new Cookie($cookieName, 'true', time() + (10 * 365 * 24 * 60 * 60));
        $response = new RedirectResponse($this->generateUrl('sonata_admin_dashboard'));

        $response->headers->setCookie($cookie);

        return $response;
    }
}