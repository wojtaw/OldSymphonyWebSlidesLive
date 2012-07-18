<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller {   

    /* List of all structures that this Controller strores in the Session
    * AccessControl accessControl
    * Boolean       LangSetManualy
    *                     
    */                            
                                  
    protected $data;
                                              
    public function __construct () {
      $this->data = array();          
    }
    
    public function start() {
      $this->setLocale();
      $this->initAccessControl();     
    }
    
    public function setLocale() {
      $request = $this->getRequest();
      $session = $request->getSession();
      
      if (!$session->has('LangSetManualy')) {
        $langs = $request->getLanguages();
        if (!is_array($langs) || empty($langs) || !isset($langs[0])) {
          $langs = null;                                                                   
        }
        $preferredLang = $request->getPreferredLanguage($langs);
        if (!is_null($preferredLang)) {
          $session->setLocale($preferredLang);
        }
      }
    }
    
    public function initAccessControl() {
      $session = $this->getRequest()->getSession();
      $context = $this->get('security.context'); 
      if (!$session->has('accessControl')) {
        $session->set('accessControl', null);
      }
      if ($context->isGranted('ROLE_USER')) {
        $channel = $context->getToken()->getUser()->getChannel();
        $session->set('accessControl', new AccessControl($channel));
      }
      else {
        $ac = $session->get('accessControl'); 
        if (!empty($ac) && !$ac->isPrivateLinkAccess()) { 
          $session->set('accessControl', null);
        }
      }
    }
                     
}
