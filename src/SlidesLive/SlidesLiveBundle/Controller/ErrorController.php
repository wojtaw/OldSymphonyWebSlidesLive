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
			"1001" => "Jeej category ERROR_SOURCE_TITLE does not exists!",
			"1002" => "Ouch... account ERROR_SOURCE_TITLE does not exists!",
			"1003" => "Whoops... folder ERROR_SOURCE_TITLE does not exists!",			
			"1004" => "jejejejej... presentation ERROR_SOURCE_TITLE is missing!",						
		);					
	}                                      
                                                  
    public function newErrorPageAction($errorCode,$errorSourceTitle) {
		$errorTitle = $this->errorCodes[$errorCode];  
		$errorTitle = str_replace("ERROR_SOURCE_TITLE",$errorSourceTitle,$errorTitle);
		$this->data['errorTitle'] = $errorTitle;
		return $this->render('SlidesLiveBundle:Error:newErrorPage.html.twig', $this->data);    
    }             
}
