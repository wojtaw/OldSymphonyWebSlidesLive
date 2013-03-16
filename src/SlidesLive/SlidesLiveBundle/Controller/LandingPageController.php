<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LandingPageController extends Controller {

    protected $data = array();                                         
                                                  
    public function schoolAction() {
        return $this->render('SlidesLiveBundle:LandingPage:school.html.twig', $this->data);
    }
	
    public function speakerAction() {
        return $this->render('SlidesLiveBundle:LandingPage:speaker.html.twig', $this->data);
    }	
	
    public function conferenceAction() {
        return $this->render('SlidesLiveBundle:LandingPage:conference.html.twig', $this->data);
    }	
                     
}
