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
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\File;

class AccountController extends Controller
{

    protected $data = array();                                         
    
    public function accountEditFormAction(Request $request) {
      $account = $this->get('security.context')->getToken()->getUser();
      $this->data['message'] = '';      
      
      $form = $this->createForm(new AccountEditForm(), $account);
      
      if ($request->getMethod() == 'POST' && isset($_POST['accountEdit'])) {
        $form->bindRequest($request);
        $account->canonizeName();
        if ($form->isValid()) {
          $em = $this->getDoctrine()->getEntityManager();
          $em->flush();
          $this->data['message'] = 'Account info successfully saved.';
        }
      }  
      
      $this->data['accountEditForm'] = $form->createView();    
      return $this->render('SlidesLiveBundle:Account:accountEditForm.html.twig', $this->data);    
    }
    
    public function passwordChangeFormAction($action) {
      $request = $this->getRequest();
      $account = $this->get('security.context')->getToken()->getUser();
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
        $encoder = $this->get('security.encoder_factory')->getEncoder($account);
        $data['old_password'] = $encoder->encodePassword($data['old_password'], $account->getSalt());
        if ($data['old_password'] != $account->getPassword()) {
          $form->addError(new FormError("The old password is not valid."));
        }
        if ($form->isValid()) {
          $em = $this->getDoctrine()->getEntityManager();
          $account->setPassword($data['new_password']);
          $account->encodePassword($this);
          //$account->setPurpose('heslo: '.$data['new_password']);
          $em->flush();
          $this->data['message'] = 'Password successfully changed.';
        }
      }  
      
      $this->data['form'] = $form->createView();
      return $this->render('SlidesLiveBundle:Account:passwordChangeForm.html.twig', $this->data);    
    }
                                             
    public function manageAccountAction() {                            
        
        $this->data['accountEditForm'] = $this->forward('SlidesLiveBundle:Account:accountEditForm');
        $this->data['passwordChangeForm'] = $this->forward('SlidesLiveBundle:Account:passwordChangeForm', array( 'action' => $this->generateUrl('manageAccount')));
        $this->data['uploadBackground'] = $this->forward('SlidesLiveBundle:Account:uploadImage', array('type' => 'background-images'));
        
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
      return $this->render('SlidesLiveBundle:Account:presentationEditForm.html.twig', $this->data);    
    }
    
    public function managePresentationsAction($presentationId) {
        $presentations = $this->get('security.context')->getToken()->getUser()->getPresentations();
        if (count($presentations) < 1) {
          $this->data['presentations'] = null;        
        }
        else {
          $this->data['presentations'] = $presentations; 
        }
    
        $this->data['presentationEditForm'] = '';
        if ($presentationId != -1) {
          $presentation = $this->getDoctrine()->getEntityManager()->getRepository('SlidesLiveBundle:Presentation')->find($presentationId);
          if (empty($presentation)) {
            $this->get('session')->setFlash('notice', "Presentation with id $presentationId does not exist.");
          }
          else {
            $this->data['presentationEditForm'] = $this->forward('SlidesLiveBundle:Account:presentationEditForm', array(
                                                                                                              'presentation' => $presentation,
                                                                                                              'action' => $this->generateUrl('managePresentations', array('presentationId' => $presentationId))
                                                                                                            )
                                                                 );
          }
        }                
        return $this->render('SlidesLiveBundle:Account:managePresentations.html.twig', $this->data);
    }
    
    public function uploadImageAction(Request $request, $type) {
        $customErrors = "";
        $constraintCollection = new Collection(array(
          'file' => new File(array(
            'maxSize' => 20*1024*1024,
          ))
        ));
        $form = $this->createFormBuilder(null, array('validation_constraint' => $constraintCollection))
            ->add('file', 'file')
            ->getForm();
    
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $file = $data['file'];
                print_r($data);
                $account = $this->get('security.context')->getToken()->getUser();
                $extension = 'jpg';
                $file->move('./data/accounts/'.$type.'/', sprintf("%d.%s", $account->getId(), $extension));
            }
        }
        return $this->render('SlidesLiveBundle:Account:uploadForm.html.twig', array('form' => $form->createView(), 'errors' => $customErrors));
    }
                     
}
                                                          