<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller {

    protected $data = array();                                         
                                              
    public function loginAction() {
      return $this->render('SlidesLiveBundle:Login:login.html.twig');    
    }
                     
}
