<?php

namespace Meta\MetaBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Meta\MetaBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Meta\MetaBundle\Entity\Other\AccessControl;


class PrivateLinkController extends BaseController
{                                         
    protected $data;
    
    public function __construct() {
      parent::__construct();
    }
                                              
    public function channelAction($code) {
      $this->start();
      $session = $this->get('session');
      $repository = $this->getDoctrine()->getEntityManager()->getRepository('MetaBundle:Channel');
      $channel = $repository->findOneByPrivateCode($code);
      if (empty($channel)) {
        $session->setFlash('notice', 'Invalid web address.');
        return $this->redirect($this->generateUrl('_welcome'));
      }
      $ac = new AccessControl($channel);
      $ac->setPrivateLinkAccess(true); 
      $session->set('accessControl', $ac);
      return $this->redirect($this->generateUrl('channelPage', array('channelName' => $channel->getCanonicalname())));    
    }
                     
}
