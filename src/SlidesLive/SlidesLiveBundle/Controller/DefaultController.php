<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SlidesLive\SlidesLiveBundle\Controller\BaseController;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;

class DefaultController extends Controller { 

  protected $data = array(
    //'privacy_mode' => Privacy::p_public(),
    'stylesheet' => null,
    'account' => null,
    'presentation' => null,
    'folder' => null,
    'folderPresentations' => array(),
  );

  public function accountAction($accountCanName) {
    //return new Response('Controller: Default, Action: account,<br />$accountCanName: '.$accountCanName);
    $accountRepo = $this->getDoctrine()->getEntityManager()->getRepository('SlidesLiveBundle:Account');
    $this->data['account'] = $accountRepo->findByCanonicalName($accountCanName);
    if (!$this->data['account']) {
      return $this->redirect($this->generateUrl('errorPage', array('message' => "We are sorry, but $accountCanName does not exist.")));
    }
    $this->data['account'] = $this->data['account'][0];
     
    return $this->render('SlidesLiveBundle:Default:playerPage.html.twig', $this->data);
  }
  
  public function folderAction($accountCanName, $folderCanName) {
    return new Response("Controller: Default, Action: folderAction,<br />\$accountCanName: $accountCanName,<br />\$folderCanName: $folderCanName"); 
    //return $this->render('');
  }
  
  public function presentationAction($accountCanName, $folderCanName, $presentationId) {
    return new Response("Controller:Default , Action: presentation,<br />\$accountCanName: $accountCanName,<br />\$folderCanName: $folderCanName,<br />\$presentationId: $presentationId"); 
    //return $this->render('');
  }
                                                                  
}

