<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends FOSRestController
{
    public function getContentAction($id)
    {
        var_dump($id);
        return new Response(json_encode(['test'=>'mytest']));
    } // "get_content"   [GET] /content/{id}

}
