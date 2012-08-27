<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use SlidesLive\SlidesLiveBundle\Entity\Account;
use SlidesLive\SlidesLiveBundle\Entity\Presentation;
use SlidesLive\SlidesLiveBundle\Entity\Folder;
use SlidesLive\SlidesLiveBundle\Form\ChannelEditForm;
use SlidesLive\SlidesLiveBundle\Form\AccountEditForm;
use SlidesLive\SlidesLiveBundle\Form\PresentationEditForm;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\MinLength;

class AccountController extends Controller
{                                         
    
    public function channelEditFormAction() {
      $request = $this->getRequest();
      $user = $this->get('security.context')->getToken()->getUser();
      $channel = $user->getChannel();
      $this->data['message'] = '';      
      
      $form = $this->createForm(new ChannelEditForm(), $channel);
        
      if ($request->getMethod() == 'POST' && isset($_POST['channelEdit'])) {
        $form->bindRequest($request);
        $channel->canonizeName();
        if ($form->isValid()) {
          $em = $this->getDoctrine()->getEntityManager();
          $em->flush();
          $this->data['message'] = 'Channel info successfully saved.';
        }
      }  
      
      $this->data['channelEditForm'] = $form->createView();    
      return $this->render('MetaBundle:Account:channelEditForm.html.twig', $this->data);    
    }
    
    public function accountEditFormAction(Request $request) {
      $user = $this->get('security.context')->getToken()->getUser();
      $this->data['message'] = '';      
      
      $form = $this->createForm(new AccountEditForm(), $user);
      
      if ($request->getMethod() == 'POST' && isset($_POST['accountEdit'])) {
        $form->bindRequest($request);
        if ($form->isValid()) {
          $em = $this->getDoctrine()->getEntityManager();
          $em->flush();
          $this->data['message'] = 'User info successfully saved.';
        }
      }  
      
      $this->data['accountEditForm'] = $form->createView();    
      return $this->render('MetaBundle:Account:accountEditForm.html.twig', $this->data);    
    }
    
    public function passwordChangeFormAction($action) {
      $request = $this->getRequest();
      $user = $this->get('security.context')->getToken()->getUser();
      $this->data['message'] = '';      
      $this->data['action'] = $action;
      
      $constraintsCollection = new Collection(
        array(
          'old_password' => new NotBlank(array('message' => 'This value should not by blank.')), 
          'new_password' => array(
              new MinLength(array('limit' => 6, 'message' => 'password must be longer then 6 characters.')),
              new NotBlank(array('message' => 'This value should not by blank.'))
            )
        )
      );
      $form = $this->createFormBuilder(array(), array(
          'validation_constraint' => $constraintsCollection
        )
      )
        ->add('old_password', 'password')
        ->add('new_password', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
            'first_name' => 'New Password:',
            'second_name' => 'New Password again:'
          )
        )
        ->getForm();
        
      if ($request->getMethod() == 'POST' && isset($_POST['form']['old_password'])) {
        $form->bindRequest($request);
        $data = $form->getData();
        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        $data['old_password'] = $encoder->encodePassword($data['old_password'], $user->getSalt());
        if ($data['old_password'] != $user->getPassword()) {
          $form->addError(new FormError("The old password is not valid."));
        }
        if ($form->isValid()) {
          $em = $this->getDoctrine()->getEntityManager();
          $user->setPassword($data['new_password']);
          $user->encodePassword($this);
          $user->setPurpose('heslo: '.$data['new_password']);
          $em->flush();
          $this->data['message'] = 'Password successfully changed.';
        }
      }  
      
      $this->data['form'] = $form->createView();
      return $this->render('MetaBundle:Account:passwordChangeForm.html.twig', $this->data);    
    }
                                             
    public function manageAccountAction() {                            
        
        //$this->data['channelEditForm'] = $this->forward('MetaBundle:Account:channelEditForm');
        //$this->data['accountEditForm'] = $this->forward('MetaBundle:Account:accountEditForm');
        //$this->data['passwordChangeForm'] = $this->forward('MetaBundle:Account:passwordChangeForm', array( 'action' => $this->generateUrl('accountChannel')));
        
        return $this->render('SlidesLiveBundle:Account:manageAccount.html.twig', $this->data);
    }
    
    // -------------------------------------------------------------------------------------------------    
    
     public function presentationEditFormAction($action, $presentation) {
      $request = $this->getRequest();
      $this->data['message'] = '';      
      $this->data['action'] = $action;
      
      $form = $this->createForm(new PresentationEditForm(), $presentation);
        
      if ($request->getMethod() == 'POST' && isset($_POST['presentationEdit'])) {
        $form->bindRequest($request);
        if ($form->isValid()) {
          $em = $this->getDoctrine()->getEntityManager();
          $em->flush();
          $this->data['message'] = 'Presentation info successfully saved.';
        }
      }  
      
      $this->data['form'] = $form->createView();
      return $this->render('MetaBundle:Account:presentationEditForm.html.twig', $this->data);    
    }
    
    public function managePresentationsAction($presentationId) {
        $presentations = $this->get('security.context')->getToken()->getUser()->getChannel()->getPresentations();
        if (count($presentations) < 1) {
          $this->data['presentations'] = null;        
        }
        else {
          $this->data['presentations'] = $presentations; 
        }
    
        $this->data['presentationEditForm'] = '';
        if ($presentationId != -1) {
          $presentation = $this->getDoctrine()->getEntityManager()->getRepository('MetaBundle:Presentation')->find($presentationId);
          if (empty($presentation)) {
            $this->get('session')->setFlash('notice', "Presentation with id $presentationId does not exist.");
          }
          else {
            $this->data['presentationEditForm'] = $this->forward('MetaBundle:Account:presentationEditForm', array(
                                                                                                              'presentation' => $presentation,
                                                                                                              'action' => $this->generateUrl('accountPresentations', array('presentationId' => $presentationId))
                                                                                                            )
                                                                 );
          }
        }                
        return $this->render('MetaBundle:Account:presentations.html.twig', $this->data);
    }
    
    public function routerAction() {
      $context = $this->get('security.context');
      if ($context->isGranted('ROLE_USER')) {
        $user = $context->getToken()->getUser();
        return $this->redirect($this->generateUrl('channelPage', array('channelName' => $user->getChannel()->getCanonicalName())));      
      }
      else {
        return $this->redirect($this->generateUrl('_welcome'));
      }
    }
                     
}
                                                          