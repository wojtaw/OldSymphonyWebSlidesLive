<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;

class BaseController extends Controller {                               
                                  
    protected $data = array(
      'privacy_mode' => Privacy::P_PUBLIC,
    );
                     
}
