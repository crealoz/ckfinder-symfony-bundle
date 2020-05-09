<?php

namespace CKSource\CKFinder\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlHelper
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function generate($type =  null)
    {
        // generate a URL with route arguments
        return $this->router->generate('ckfinder_connector_downloader', [
            'type' => $type,
        ]);
    }
}