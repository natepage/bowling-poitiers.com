<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\SerializationContext;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
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
     * @Route(
     *     "/get/posts/{page}",
     *     name="api_rest_get_posts",
     *     requirements={"page": "\d+"},
     *     defaults={"page": 1}
     * )
     */
    public function getPostsAction($format, $page)
    {
        $query = $this->getDoctrine()->getRepository('AppBundle:Post')->getIndexQuery();
        $paginator = $this->get('knp_paginator');

        $limit = 10;
        $pagination = $paginator->paginate($query, $page, $limit);

        if($pagination instanceof SlidingPagination){
            $hasMore = $page < $pagination->getPageCount() ?: false;
        } else {
            $hasMore = false;
        }

        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        $data = array("posts" => array(), "has_more" => $hasMore);
        foreach($pagination as $post){
            $previewImage = $post->getPreviewImage();

            if($previewImage instanceof Image){
                $images = new ArrayCollection();
                $cachePath = $imagineCacheManager->getBrowserPath($previewImage->getWebPath(), "post_preview");

                $previewImage->setWebPath($cachePath);
                $images->add($previewImage);
                $post->setImages($images);
            }

            $data["posts"][] = $post;
        }

        return $this->generateResponse($data, $format);
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
     * @Route(
     *     "/get/image/{filter}/{webPath}",
     *     name="api_rest_get_image_cache_by_webpath",
     *     requirements={"webPath": ".+"}
     * )
     */
    public function getImageCachePathByWebpathAction($format, $filter, $webPath)
    {
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $cachePath = $imagineCacheManager->getBrowserPath($webPath, $filter);

        return $this->generateResponse(array('cache_path' => $cachePath), $format);
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
        $context = SerializationContext::create()->setSerializeNull(true);

        $response = new Response();
        $response->headers->set('Content-Type', $format);

        $response->setContent($serializer->serialize($data, $format, $context));

        return $response;
    }
}