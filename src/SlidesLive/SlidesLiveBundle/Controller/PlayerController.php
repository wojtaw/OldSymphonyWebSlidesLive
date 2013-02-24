<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use SlidesLive\SlidesLiveBundle\Entity\Note;
use SlidesLive\SlidesLiveBundle\Entity\Account;
use SlidesLive\SlidesLiveBundle\Entity\Presentation;

class PlayerController extends Controller {

    protected $data = array();                                         
                                              
    public function userAuthAction() {
		//Check if user is logged to the site
		$securityContext = $this->container->get('security.context');
		if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
			return new Response('LOGGED',200);			
		} else {			
			return new Response('ANONYMOUS',200);  			
		}
    }
	
	public function addNoteAction(Request $request) {
		$presentationID = $request->request->get('presentationID');
		$noteContent = $request->request->get('noteContent');
		$timecode = $request->request->get('timecode');		
		if($presentationID == null)	return new Response('Presentation ID is not included',412);
		if($noteContent == null) return new Response('Note content is not included',412);
		if($timecode == null) return new Response('Timecode is not included',412);		

		//Check if user is logged, and find what user it is or return 401 CODE
		$securityContext = $this->container->get('security.context');
		if( !$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
			return new Response('NOT LOGGED IN',401);				
		}
		
		//Find account and presentation and validate them
		$em = $this->getDoctrine()->getEntityManager();	
		
		$accountId = $this->get('security.context')->getToken()->getUser()->getId();
		
		$account = $em->getRepository('SlidesLiveBundle:Account')->find($accountId);
		$presentation = $em->getRepository('SlidesLiveBundle:Presentation')->find($presentationID);
		
		if($presentation == null) return new Response('Presentation does not exists',404);	
		if($account == null) return new Response('Account does not exists',404);			
		
		//Create new note with content
		$note = new Note();
		$note->setTimecode($timecode);
		$note->setTextContent($noteContent);	
		$note->setAccount($account);
		$note->setPresentation($presentation);					
		
		//Save stuff to database
		$em->persist($note);
		$em->flush();		
		
		return new Response('NOTE ADDED',200);  		 		
	}
                     
}
