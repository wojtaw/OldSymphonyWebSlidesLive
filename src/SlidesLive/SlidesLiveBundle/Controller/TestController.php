<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller {

    protected $data = array();                                         
                                                  
    public function linkBrowserAction($accountCanName) {
      $account = $this->getDoctrine()->getRepository('SlidesLiveBundle:Account')
        ->findByCanonicalName($accountCanName);
      if (count($account) != 1) {
        $this->data['account'] = null;
      }
      else {
        $this->data['account'] = $account[0];
      }      
      return $this->render('SlidesLiveBundle:Test:linkBrowser.html.twig', $this->data);   
    }
                     
}
