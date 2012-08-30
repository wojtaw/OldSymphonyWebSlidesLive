<?php

namespace SlidesLive\StaticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller {

    protected $data = array();                                         
                                                  
    public function errorPageAction($message) {
      $this->data['message'] = $message;      
      return $this->render('StaticBundle:Error:errorPage.html.twig', $this->data);    
    }
                     
}
