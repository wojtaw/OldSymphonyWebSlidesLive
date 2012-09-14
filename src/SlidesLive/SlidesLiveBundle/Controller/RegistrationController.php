<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;    
use SlidesLive\SlidesLiveBundle\Entity\Account;
use SlidesLive\SlidesLiveBundle\Entity\Folder;
use SlidesLive\SlidesLiveBundle\Form\RegistrationForm;

class RegistrationController extends Controller {     

    protected $data = array();                                    
                                              
    public function registrationAction() {
      $request = $this->getRequest();
      $validator = $this->get('validator');
      $account = new Account();      
      
      $form = $this->createForm(new RegistrationForm(), $account);
        
      if ($request->getMethod() == 'POST') {
        $form->bindRequest($request);
        $account->canonizeName();                         
        $canonicalNameErrors = $validator->validate($account);
        if (count($canonicalNameErrors) > 0) {
          $form->addError(new FormError("The account name, you have entered, is quite similiar to an existing one. Please change the name of your account."));          
        }
        if ($form->isValid()) {
          $em = $this->getDoctrine()->getEntityManager();
          $folder = new Folder();
          $folder->setName("Default");
          $folder->canonizeName();
          $folder->setAccount($account);
          $account->encodePassword($this);
          $account->addFolder($folder);
          $account->setPrimaryFolder($folder);
          $em->persist($folder);
          $em->persist($account);
          $em->flush();
          
          return $this->render('SlidesLiveBundle:Registration:result.html.twig', $this->data);
        }
      }  
      
      $this->data['form'] = $form->createView();    
      $this->data['form_content'] = print_r($form->createView(), true); // TO JE CO???
      
      return $this->render('SlidesLiveBundle:Registration:registration.html.twig', $this->data);    
    }
     
    public function resultAction() {
      return $this->render('SlidesLiveBundle:Registration:result.html.twig', $this->data);
    } 
                     
}
