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
		
		//Find account and presentation
		$em = $this->getDoctrine()->getEntityManager();	
		
		$accountId = $this->get('security.context')->getToken()->getUser()->getId();
		
		$account = $em->getRepository('SlidesLiveBundle:Account')->find($accountId);
		
		echo $account->getUsername();
		//Create new note with content
		$note = new Note();
		$note->setTimecode($timecode);
		$note->setTextContent($noteContent);	
		
		
		//Save stuff to database
		
		
		//Find the presentation
		
		//Add note connecting presentation and user
		

		/*
        $repository = $this->getDoctrine()->getRepository('SlidesLiveBundle:Presentation');
        $this->data = array (
          'presentation' => null,
          'width' => null,
          'player' => null
        );

        
        $this->data['presentation'] = $repository->find($presentationId);
        if ($this->data['presentation'] == null) {
          return $this->render('SlidesLiveBundle:Embed:embedPlayer.html.twig', $this->data);                                                                                        
        }
		
		if($customWidth == null) $this->data['width'] = 960;
		else $this->data['width'] = $customWidth;	
        
         
        if ($this->data['presentation']->getVideo()) {
          if (isset($_GET['player']) && ($_GET['player'] == "audio" || $_GET['player'] == "video")) {
            $this->data['player'] = $_GET['player'];        
          }        
        }
        else {
          $this->data['player'] = "audio";        
        }
		*/

        
        //return $this->render('SlidesLiveBundle:Embed:embedPlayer.html.twig', $this->data);   
			return new Response('TEST',200);  		 		
	}
                     
}
