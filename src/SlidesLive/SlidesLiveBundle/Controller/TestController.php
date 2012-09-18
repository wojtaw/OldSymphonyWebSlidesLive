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

    public function passwordChangeAction($accountCanName, $password) {
      $em = $this->getDoctrine()->getEntityManager();
      $results = $em->getRepository('SlidesLiveBundle:Account')->findByCanonicalName($accountCanName);
      if (count($results) != 1) {
        print("Account not found.<br />\n");        
      }
      else {
        $account = $results[0];
        print("Accont: ".$account->getName()."<br />\n");
        print("Password: $password<br />\n");
        $encoder = $this->get('security.encoder_factory')->getEncoder($account);
        $account->setPassword($encoder->encodePassword($password, $account->getSalt()));
        print("Password created.<br />\n");                
        $em->flush();
        print("Password saved.<br />\n");        
      }
      return new Response('');
    }                     
}
