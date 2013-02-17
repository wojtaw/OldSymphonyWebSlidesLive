<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class EmbedController extends Controller {

    protected $data = array();                                         
                                              
    public function embedAction($presentationId,$customWidth) {
		

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
		

        
        return $this->render('SlidesLiveBundle:Embed:embedPlayer.html.twig', $this->data);    
    }
	
    public function embedJSAction($presentationId) {
		

        $repository = $this->getDoctrine()->getRepository('SlidesLiveBundle:Presentation');
        $this->data = array (
          'presentation' => null,
          'width' => null,
          'player' => null
        );


        $this->data['presentation'] = $repository->find($presentationId);

        if ($this->data['presentation'] == null) {
			return new Response('',204);                                                                                      
        }
		
		if($this->data['presentation']->getExternalService()){
			$service = $this->data['presentation']->getExternalService();			
			$serviceID = $this->data['presentation']->getExternalMedia();
		} else {
			$service = $this->data['presentation']->getService();			
			$serviceID = $this->data['presentation']->getServiceId();
		}
		
		$jsonData = json_encode(array(
			array(
					'hasVideo' => $this->data['presentation']->getVideo(),
					'hasSlides' => $this->data['presentation']->getSlides(),
					'mediaType' => $service,
					'mediaID' => $serviceID,
					'isPaid' => $this->data['presentation']->getIsPaid()					
			),
		));
		
		$headers = array(
			'Content-Type' => 'application/json'
		);
		
		$response = new Response($jsonData, 200, $headers);
		return $response;  
    }	
                     
}
