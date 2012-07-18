<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use SlidesLive\SlidesLiveBundle\Controller\BaseController;

class DefaultController extends BaseController { 
    
    public function __construct() {
      parent::__construct();    
    }
                                              
    public function indexAction() {
        $this->start();
        $em = $this->getDoctrine()->getEntityManager();
        $session = $this->get('session');
        $this->data = array('presentations' => array());
        
        // FIX ME - predelat na timeline
        $this->data['presentations'] = $em->getRepository('MetaBundle:Presentation')->list18NewestPresentations($session->get('accessControl'));
        if (count($this->data['presentations']) < 1) {
            $session->setFlash('notice','There are no available presentations now.');
            return $this->render('MetaBundle:Default:index.html.twig', $this->data);           
        }
        
        return $this->render('MetaBundle:Default:index.html.twig', $this->data);               
    }
    
                     
}

