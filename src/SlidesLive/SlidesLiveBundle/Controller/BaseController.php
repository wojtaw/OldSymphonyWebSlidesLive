<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller {                               
                                  
    protected $data;
    protected $mode;
                                              
    public function __construct () {
      $this->data = array();          
    }
                     
}
