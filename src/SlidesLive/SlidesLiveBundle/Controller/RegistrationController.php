<?php

namespace Meta\MetaBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Meta\MetaBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Meta\MetaBundle\Entity\User;
use Meta\MetaBundle\Entity\Channel;
use Meta\MetaBundle\Entity\Folder;
use Meta\MetaBundle\Form\RegistrationForm;

class RegistrationController extends BaseController
{                                         
    
    public function __construct() {
      parent::__construct();
    }
                                              
    public function registrationAction() {
      $this->start();
      $request = $this->getRequest();
      $validator = $this->get('validator');
      $user = new User();      
      
      $form = $this->createForm(new RegistrationForm(), $user);
        
      if ($request->getMethod() == 'POST') {
        $form->bindRequest($request);
        $user->getChannel()->canonizeName();
        $canonicalNameErrors = $validator->validate($user->getChannel());
        if (count($canonicalNameErrors) > 0) {
          $form->addError(new FormError("The channel name, you have entered, is quite similiar to an existing one. Please change the name of your channel."));          
        }
        if ($form->isValid()) {
          $em = $this->getDoctrine()->getEntityManager();
          $user->encodePassword($this);
          $folder = new Folder();
          $folder->setName("Default");
          $folder->canonizeName();
          $folder->setChannel($user->getChannel());
          $user->getChannel()->addFolder($folder);
          $user->getChannel()->setPrimaryFolder($folder);
          $em->persist($folder);
          $em->persist($user->getChannel());
          $em->persist($user);
          $em->flush();
          
          return $this->render('MetaBundle:Registration:result.html.twig', $this->data);
        }
      }  
      
      $this->data['form'] = $form->createView();    
      $this->data['form_content'] = print_r($form->createView(), true);
      return $this->render('MetaBundle:Registration:registration2.html.twig', $this->data);    
    }
     
    public function resultAction() {
      $this->start();
      return $this->render('MetaBundle:Registration:result.html.twig', $this->data);
    } 
                     
}
