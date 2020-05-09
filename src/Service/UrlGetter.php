<?php

namespace CKSource\CKFinder\Service;

class UrlGetter
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function generate($type =  null)
    {
        // generate a URL with route arguments
        $userProfilePage = $this->router->generate('ckfinder_connector_downloader', [
            'type' => $type,
        ]);
    }
}