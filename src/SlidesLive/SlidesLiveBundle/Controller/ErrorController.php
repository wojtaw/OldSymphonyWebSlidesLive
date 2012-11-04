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
    protected $errorCodes;  	
		 
  
	public function __construct() {
		$this->errorCodes = array(
			"1001" => "Jeej this category does not exists!",
			"1002" => "",
		);					
	}                                      
                                                  
    public function newErrorPageAction($errorCode) {
      $this->data['errorTitle'] = $this->errorCodes[$errorCode];  
	  
      return $this->render('SlidesLiveBundle:Error:newErrorPage.html.twig', $this->data);    
    }             
}
