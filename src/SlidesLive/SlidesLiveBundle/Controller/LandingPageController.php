<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LandingPageController extends Controller {

    protected $data = array();                                         
                                                  
    public function schoolsAction() {
        return $this->render('SlidesLiveBundle:LandingPage:schools.html.twig', $this->data);
    }
                     
}
