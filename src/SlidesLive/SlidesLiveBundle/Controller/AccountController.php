<?php

/* TODO
  - odstranovani nahranych souboru podle id a ne podle koncovky (co kdyz tam nekdo nahraje treba .txt)
  - osetrit validaci na typ nahravanych souboru
*/

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\FormError;
use SlidesLive\SlidesLiveBundle\Entity\Account;
use SlidesLive\SlidesLiveBundle\Entity\Presentation;
use SlidesLive\SlidesLiveBundle\Entity\Folder;
use SlidesLive\SlidesLiveBundle\Form\ChannelEditForm;
use SlidesLive\SlidesLiveBundle\Form\AccountEditForm;
use SlidesLive\SlidesLiveBundle\Form\PresentationEditForm;
use SlidesLive\SlidesLiveBundle\Form\UploadForm;
use SlidesLive\SlidesLiveBundle\Form\BackgroundUploadForm;
use SlidesLive\SlidesLiveBundle\Form\LogoUploadForm;
use SlidesLive\SlidesLiveBundle\Form\AvatarUploadForm;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\File;

class AccountController extends Controller
{

    protected $data = array();            

    protected function setFolder() {

    }                             
    
    public function accountEditFormAction(Request $request) {
      $em = $this->getDoctrine()->getEntityManager();
      $account = $this->get('security.context')->getToken()->getUser();
      $this->data['message'] = '';      
      
      $form = $this->createForm(new AccountEditForm($account), $account);
      
      if ($request->getMethod() == 'POST' && isset($_POST['accountEdit'])) {
        $form->bindRequest($request);
        $oldPassword = $form->get('old_password')->getData();
        $newPassword = $form->get('new_password')->getData();
        $encoder = $this->get('security.encoder_factory')->getEncoder($account);
        $encodedOldPassword = $encoder->encodePassword($oldPassword, $account->getSalt());
        $account->canonizeName();
        if ($oldPassword) { 
          if ($encodedOldPassword != $account->getPassword()) {
            $form->get('old_password')->addError(new FormError("The old password is not valid."));
          }
          else if (!$newPassword) {
            $form->get('new_password')->addError(new FormError("New password not inserted."));          
          }            
        }
        // nastaveni vybraneho primary folderu podle zadaneho id
        $folderId = $form->get('primaryFolderId')->getData();
        $folder = $em->getRepository('SlidesLiveBundle:Folder')->find($folderId);
        if (!$folder) {
          $form->get('primaryFolderId')->addError(new FormError("Inserted folder does not exist."));          
        } 
        else {
          $account->setPrimaryFolder($folder);
        }
        if ($form->isValid()) {
          if ($oldPassword && $newPassword) {
            $account->setPassword($newPassword);
            $account->encodePassword($this);
          }
          $em->flush();
          $this->data['message'] = 'Account info successfully saved.';
        }
      }  
      $this->data['accountEditForm'] = $form->createView();    
      //print_r($this->data['accountEditForm']['primaryFolderId']);
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
              new MinLength(array('limit' => 6, 'message' => 'Password must be longer then 6 characters.')),
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
        //$this->data['passwordChangeForm'] = $this->forward('SlidesLiveBundle:Account:passwordChangeForm', array( 'action' => $this->generateUrl('manageAccount')));
        $this->data['uploadBackgroundForm'] = $this->forward('SlidesLiveBundle:Account:uploadImage', array('type' => 'background-images', 'formClass' => new BackgroundUploadForm()));
        $this->data['uploadLogoForm']       = $this->forward('SlidesLiveBundle:Account:uploadImage', array('type' => 'logos', 'formClass' => new LogoUploadForm()));
        $this->data['uploadAvatarForm']     = $this->forward('SlidesLiveBundle:Account:uploadImage', array('type' => 'avatars', 'formClass' => new AvatarUploadForm()));
        
        return $this->render('SlidesLiveBundle:Account:manageAccount.html.twig', $this->data);
    }
    
    // -------------------------------------------------------------------------------------------------
    
    public function uploadThumbnailAction(Request $request, Presentation $presentation) {
        $form = $this->createForm(new UploadForm());
        $message = '';
    
        if ($request->getMethod() == 'POST' && isset($_POST[$form->getName()])) {
            $form->bindRequest($request);
            $data = $form->getData();
            $file = $data['file'];
            if ($form->isValid() && $file && $file->isValid()) {
              // odstraneni puvodniho souboru
              $this->deleteOldFiles('/data/PresentationThumbs', $presentation->getId().'\.*');    
              // ulozeni noveho souboru 
              $extension = $this->extractExtension($file->getClientOriginalName());
              $file->move($_SERVER['DOCUMENT_ROOT'].'/data/PresentationThumbs/', sprintf("%d.%s", $presentation->getId(), $extension));
            }
            else {  // soubor se nepodarilo nahrat
              $message = 'The file upload failed.';            
            }
        }
        return $this->render('SlidesLiveBundle:Account:uploadForm.html.twig', array('form' => $form->createView(), 'message' => $message));
    }    
    
     public function presentationEditFormAction($action, $presentation) {
      $request = $this->getRequest();
      $account = $this->get('security.context')->getToken()->getUser();
      $this->data['message'] = '';      
      $this->data['action'] = $action;
      
      $form = $this->createForm(new PresentationEditForm($account), $presentation);
        
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
        $this->data['thumbnailUploadForm'] = '';
        if ($presentationId != -1) {
          $presentation = $this->getDoctrine()->getEntityManager()->getRepository('SlidesLiveBundle:Presentation')->find($presentationId);
          if (empty($presentation)) {
            $this->data['presentation'] = null;
            $this->get('session')->setFlash('notice', "Presentation with id $presentationId does not exist.");
          }
          else {
            $this->data['presentation'] = $presentation;
            $this->data['presentationEditForm'] = $this->forward('SlidesLiveBundle:Account:presentationEditForm', array(
                                                                                                              'presentation' => $presentation,
                                                                                                              'action' => $this->generateUrl('managePresentations', array('presentationId' => $presentationId))
                                                                                                            )
                                                                 );
            $this->data['thumbnailUploadForm'] = $this->forward('SlidesLiveBundle:Account:uploadThumbnail', array('presentation' => $presentation));
          }
        }                
        return $this->render('SlidesLiveBundle:Account:managePresentations.html.twig', $this->data);
    }
    
    // -------------------------------------------------------------------------
    
    public function deleteAccountImageAction($type) {
        $account = $this->get('security.context')->getToken()->getUser();
        $this->deleteOldFiles('/data/accounts/'.$type, $account->getId().'\.*');
        return $this->redirect($this->generateUrl('manageAccount'));            
    }
    
    // -------------------------------------------------------------------------
    
    public function uploadImageAction(Request $request, $type, $formClass) {
        $form = $this->createForm($formClass);
        $message = '';
    
        if ($request->getMethod() == 'POST' && isset($_POST[$formClass->getName()])) {
            $form->bindRequest($request);
            $data = $form->getData();
            if ($form->isValid() && $data['file']) {
              $account = $this->get('security.context')->getToken()->getUser();
              // odstraneni puvodniho souboru
              $oldFile = $account->getImage($type);
              $this->deleteOldFiles('/data/accounts/'.$type, $account->getId().'\.*');    
              // ulozeni noveho souboru 
              $file = $data['file'];
              $extension = $this->extractExtension($file->getClientOriginalName());
              $file->move($_SERVER['DOCUMENT_ROOT'].'/data/accounts/'.$type.'/', sprintf("%d.%s", $account->getId(), $extension));
            }
            else {  // soubor se nepodarilo nahrat
              $message = 'The file upload failed.';            
            }
        }
        return $this->render('SlidesLiveBundle:Account:uploadForm.html.twig', array('form' => $form->createView(), 'message' => $message));
    }
    
    private function extractExtension($path) {
      $parts = explode('.', $path);
      if (count($parts) < 1) {
        return null;      
      }
      else {
        return $parts[count($parts) - 1];      
      }    
    }
    
    /**
     * Vymazani vsech souboru nachazejicich se na zadane ceste a majicich zadane jmeno (rekurzivni prohledavani).    
     * @param $path - relativni cesta k mistu, odkud se maji zacit hledat soubory zadaneho jmena
     * @param $fileName - regularni vyraz popisujici jmeno(a) hledaneho souboru     
     */         
    private function deleteOldFiles($path, $fileName) {
      //print_r("Deleting old files ...\n");
      $finder = new Finder();
      $finder ->files()
              ->in($_SERVER['DOCUMENT_ROOT'].$path)
              ->followLinks()
              ->Name($fileName);
      //print_r(iterator_count($finder)." results found.\n");              
      foreach ($finder as $file) {
        //print_r(" - ".$file->getRealPath()."\n");
        unlink($file->getRealPath());
      }      
      //print_r("All files deleted.\n");
    }
                     
}
                                                          