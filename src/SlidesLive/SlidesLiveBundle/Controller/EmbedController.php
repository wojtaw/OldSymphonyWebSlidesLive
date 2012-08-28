<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class EmbedController extends Controller {

    protected $data = array();                                         
                                              
    public function embedAction() {
        $repository = $this->getDoctrine()->getRepository('SlidesLiveBundle:Presentation');
        $this->data = array (
          'presentation' => null,
          'width' => null,
          'player' => null
        );
        
        //echo "<pre>\n".print_r($_GET, true)."\n</pre>\n";
        
        $this->data['presentation'] = $repository->find($presentationId);
        if ($this->data['presentation'] == null) {
          return $this->render('SlidesLiveBundle:Embed:embedPlayer.html.twig', $this->data);                                                                                        
        }
        
        if (isset($_GET['width']) && is_numeric($_GET['width'])) {
          $this->data['width'] = $_GET['width']; 
        }
         
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
                     
}
