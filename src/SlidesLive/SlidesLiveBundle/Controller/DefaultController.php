<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SlidesLive\SlidesLiveBundle\Controller\BaseController;

class DefaultController extends Controller { 

                                              
    public function indexAction()
    {
        return $this->render('SlidesLiveBundle:Homepage:index.html.twig');
    }
    
                     
}

