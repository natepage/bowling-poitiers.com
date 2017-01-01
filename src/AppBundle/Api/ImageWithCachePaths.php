<?php

namespace AppBundle\Api;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("none")
 */
class ImageWithCachePaths
{
    /**
     * @var string
     */
    private $alt;

    /**
     * @var array
     * @Serializer\SerializedName("web_paths")
     */
    private $webPaths;

    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     * @return ImageWithCachePaths
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
        return $this;
    }

    /**
     * @return array
     */
    public function getWebPaths()
    {
        return $this->webPaths;
    }

    /**
     * @param array $webPaths
     * @return ImageWithCachePaths
     */
    public function setWebPaths($webPaths)
    {
        $this->webPaths = $webPaths;
        return $this;
    }
}