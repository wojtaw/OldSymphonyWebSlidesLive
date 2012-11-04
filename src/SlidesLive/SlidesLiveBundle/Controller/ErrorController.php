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
    protected $errorCodes = array();  	
		 
  
	public function __construct() {
		$errorCodes['1001'] = "Jeej this category does not exists!";
		$errorCodes['1002'] = "";
		$errorCodes['1003'] = "";
		$errorCodes['1004'] = "";						
	}                                      
                                                  
    public function newErrorPageAction($errorCode) {
      echo $errorCode;
      $this->data['errorTitle'] = array_search('1001', $errorCodes);  
      return $this->render('SlidesLiveBundle:Error:newErrorPage.html.twig', $this->data);    
    }             
}
