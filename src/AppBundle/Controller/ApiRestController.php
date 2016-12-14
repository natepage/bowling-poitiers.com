<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(
 *     "/api_rest/{format}",
 *     requirements={"format": "json|xml"},
 *     defaults={"format": "json"}
 * )
 */
class ApiRestController extends Controller
{
    /**
     * @Route("/get/posts", name="api_rest_get_posts")
     */
    public function getPostsAction($format)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findAll();

        return $this->generateResponse($posts, $format);
    }

    /**
     * @Route(
     *     "/get/post/{slug}",
     *     name="api_rest_get_post_by_id"
     * )
     */
    public function getPostBySlugAction($format, $slug)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneBy(array('slug' => $slug));

        return $this->generateResponse($post, $format);
    }

    /**
     * Generate the response's object with the good format.
     *
     * @param string $data
     * @param string $format
     * @return Response
     */
    private function generateResponse($data, $format)
    {
        $serializer = $this->get('jms_serializer');

        $response = new Response();
        $response->headers->set('Content-Type', $format);

        $response->setContent($serializer->serialize($data, $format));

        return $response;
    }
}