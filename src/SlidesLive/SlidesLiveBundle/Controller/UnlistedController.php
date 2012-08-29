<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class UnlistedController extends Controller {

    protected $data = array();                                         
                                                  
    public function accountAction($accountCanName, $hash) {
      return new Response('Controller: Unlisted, Action: account,<br />$accountCanName: '.$accountCanName.',<br />hash: '.$hash);    
    }
    
    public function folderAction($accountCanName, $folderCanName, $hash) {
      return new Response("Controller: Unlisted, Action: folderAction,<br />\$accountCanName: $accountCanName,<br />\$folderCanName: $folderCanName, <br />\$hash: $hash");
    }
    
    public function presentationAction($accountCanName, $folderCanName, $presentationId, $hash) {
      return new Response("Controller:Unlisted , Action: presentation,<br />\$accountCanName: $accountCanName,<br />\$folderCanName: $folderCanName,<br />\$presentationId: $presentationId, <br />\$hash: $hash");
    }
                     
}
