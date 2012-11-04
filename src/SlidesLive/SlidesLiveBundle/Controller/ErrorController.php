<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/*
Error codes
1001 - Category does not exists
*/



class ErrorController extends Controller {

    protected $data = array();                                         
                                                  
    public function newErrorPageAction($errorCode) {
      echo $errorCode;
      return $this->render('StaticBundle:Error:errorPage.html.twig', $this->data);    
    }
                     
}
