<?php

namespace SlidesLive\StaticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction() {
        $this->data['presentationBoxes'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:HomepageBox')->findPublicPresentationBoxes();	
        return $this->render('StaticBundle:Homepage:index.html.twig', $this->data);
    }
    
    public function tourAction() {
        return $this->render('StaticBundle:Homepage:tour.html.twig');
    }
    
    public function aboutAction() {
        return $this->render('StaticBundle:Homepage:about.html.twig');
    }
    
    public function termsAction() {
        return $this->render('StaticBundle:Homepage:terms.html.twig');
    }
    
    public function policyAction() {
        return $this->render('StaticBundle:Homepage:policy.html.twig');
    }
	
    public function thankyouAction() {		
        return $this->render('StaticBundle:Homepage:thankYouDownload.html.twig');
    }	
	
    
// --------EXAMPLE ACCOUNT -----------------------------------------------------

    public function exampleAction() {
      return $this->render('StaticBundle:Example:example.html.twig');
    }       

}
