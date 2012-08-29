<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SlidesLive\SlidesLiveBundle\Controller\BaseController;

class DefaultController extends Controller { 

  public function accountAction($accountCanName) {
    return new Response('Controller: Default, Action: account,<br />$accountCanName: '.$accountCanName); 
    //return $this->render('');
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

