<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class Controller extends Controller {

    protected $data = array();                                         
                                                  
    public function Action() {
      return new Response('Controller: , Action: ');    
    }
                     
}
