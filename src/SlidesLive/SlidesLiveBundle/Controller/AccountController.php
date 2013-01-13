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
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;
use SlidesLive\SlidesLiveBundle\Form\ChannelEditForm;
use SlidesLive\SlidesLiveBundle\Form\AccountEditForm;
use SlidesLive\SlidesLiveBundle\Form\FolderEditForm;
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
    
    public function accountEditFormAction(Request $request, $account) {
      $em = $this->getDoctrine()->getEntityManager();
      //$account = $this->get('security.context')->getToken()->getUser();
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
        $account = $this->get('security.context')->getToken()->getUser();
        
        $this->data['accountEditForm'] = $this->forward('SlidesLiveBundle:Account:accountEditForm', array('account' => $account));
        //$this->data['passwordChangeForm'] = $this->forward('SlidesLiveBundle:Account:passwordChangeForm', array( 'action' => $this->generateUrl('manageAccount')));
        $this->data['uploadBackgroundForm'] = $this->forward('SlidesLiveBundle:Account:uploadImage', array('account' => $account, 'type' => 'background-images', 'formClass' => new BackgroundUploadForm()));
        $this->data['uploadLogoForm']       = $this->forward('SlidesLiveBundle:Account:uploadImage', array('account' => $account, 'type' => 'logos', 'formClass' => new LogoUploadForm()));
        $this->data['uploadAvatarForm']     = $this->forward('SlidesLiveBundle:Account:uploadImage', array('account' => $account, 'type' => 'avatars', 'formClass' => new AvatarUploadForm()));
        
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
              $imageName = sprintf("%d.%s", $presentation->getId(), $extension);
              $imagePath = $_SERVER['DOCUMENT_ROOT'].'/data/PresentationThumbs/';
              $file->move($imagePath, $imageName);
              $this->resizeImage($imagePath.$imageName);
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
      $em = $this->getDoctrine()->getEntityManager();
      $this->data['message'] = '';      
      $this->data['action'] = $action;
      
      $form = $this->createForm(new PresentationEditForm($account, $presentation), $presentation);
      
      if ($request->getMethod() == 'POST' && isset($_POST['presentationEdit'])) {		  
        $form->bindRequest($request);
        // nastaveni vybraneho primary folderu podle zadaneho id
        $folderId = $form->get('folder')->getData();
        $folder = $em->getRepository('SlidesLiveBundle:Folder')->find($folderId);
        if (!$folder) {
          $form->get('folder')->addError(new FormError("Inserted folder does not exist."));          
        } 
        else {
          $presentation->setFolder($folder);
        }
        if ($form->isValid()) {
          $em->flush();
          $this->data['message'] = 'Presentation info successfully saved.';
        }
      }  
      
      $this->data['form'] = $form->createView();
      return $this->render('SlidesLiveBundle:Account:presentationEditForm.html.twig', $this->data);    
    }
    
    public function managePresentationsAction($presentationId) {
      $account = $this->get('security.context')->getToken()->getUser();
      $this->data = array(
          'presentations' => null,
          'presentationEditForm' => '',
          'thumbnailUploadForm' => '',
          'folderEditForm' => '',
          'folders' => $account->getFolders(),
        );

//      $presentations = $this->get('security.context')->getToken()->getUser()->getPresentations();
		$accountSafeId = $this->get('security.context')->getToken()->getUser()->getId();
      $presentations = $this->getDoctrine()->getEntityManager()->getRepository('SlidesLiveBundle:Presentation')->findAccountPresentations($accountSafeId);
	  
      if (count($presentations) > 0) {
        $this->data['presentations'] = $presentations; 
      }
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
    public function folderEditFormAction(Request $request, $account, $folder = null) {
      $em = $this->getDoctrine()->getEntityManager();
      $this->data['message'] = '';
      $folderWasNull = false;
      if (is_null($folder)) {
        $folder = new Folder();
        $folder->setAccount($account);
        $folderWasNull = true;
      }
      
      $form = $this->createForm(new FolderEditForm(), $folder);
      if ($request->getMethod() == 'POST' && isset($_POST['folderEdit'])) {
        $form->bindRequest($request);
        $folder->canonizeName();     
        $form->bindRequest($request); // aby doslo k opetovne validaci, predtim chybelo kanonicke jmeno
        // overeni zda pro zadany account jiz neexistuje folder stejneho jmena a kanonickeho jmena
        $results = $em->getRepository('SlidesLiveBundle:Folder')
          ->findAccountFoldersByNameAndCanName($account->getId(), $folder);
        if (count($results) != 0) {
          $form->get('name')->addError(new FormError("Folder with the same or similar name already exists in this account."));
          $em->detach($folder);
        }
        if ($form->isValid()) {
          $em->persist($folder);
          $em->flush();
          if ($folderWasNull) { // po ulozeni noveho folderu vykresli opet prazdny formular
            $form = $this->createForm(new FolderEditForm(), new Folder());
            $this->data['message'] = 'Folder created.';
          }
          else {
            $this->data['message'] = 'Folder info successfully saved.';
          }
        }
      }  
      $this->data['folderEditForm'] = $form->createView();    
      return $this->render('SlidesLiveBundle:Account:folderEditForm.html.twig', $this->data);    
    }

    /**
    * Pridavani a editace folderu
    * @param folderId - id folderu, ktery ma byt upraven.
    */
    public function manageFoldersAction($folderId) {
      $session = $this->get('session');
      $em = $this->getDoctrine()->getEntityManager();
      $account = $this->get('security.context')->getToken()->getUser();
      $this->data['editing'] = false;
      // provereni zadaneho folderId
      if ($folderId == -1) {  // id folderu nezadano -> vykresleni prazneho formulare
        $folder = null;
      }
      else {  // id folderu zadano
        $folder = $em->getRepository('SlidesLiveBundle:Folder')->find($folderId);
        if (!$folder) {   // folder nenalezen
          $session->setFlash('folderActionMessage', "Folder $folderId not found.");
        }
        else {  // folder nalezen -> nacteni formulare s daty folderu k editaci
          $this->data['editing'] = true;
        }
      }
      // vykresleni formulare
      $this->data['folderEditForm'] = $this->forward('SlidesLiveBundle:Account:folderEditForm', array('folder' => $folder, 'account' => $account));
      // nacteni seznamu existujicich folderu
      $this->data['folders'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Folder')->findAccountFolders($account->getId(), Privacy::P_PRIVATE);
      return $this->render('SlidesLiveBundle:Account:manageFolders.html.twig', $this->data);
    }

    public function deleteFolderAction($folderId) {
      $session = $this->get('session');
      $em = $this->getDoctrine()->getEntityManager();
      $folder = $em->getRepository('SlidesLiveBundle:Folder')->find($folderId);
      if ($folder) {
        $presentationCount = count($folder->getPresentations());
        if ($presentationCount > 0) { // folder nelze smazat protoze neni prazdny
          $session->setFlash('folderActionMessage', "Folder contains $presentationCount presentations. Only empty folder could be deleted.");
        }
        else {  // folder je prazdny
          $account = $folder->getAccount();
          if (count($account->getFolders()) <= 1) { // account obsahuje jen jednu slozku -> nelze smazat, musi mit alespon jednu
            $session->setFlash('folderActionMessage', 'Account must have at least one folder.');   
          }
          else {  // account ma vice folderu
            if ($account->getPrimaryFolder()->getId() == $folder->getId()) {  // mazu primary folder
              $session->setFlash('folderActionMessage', "You are deleting primary folder of your account. Before deleting this folder you have to choose another primary folder.");
            }
            else {  // vse OK -> mazani
              $em->remove($folder);
              $em->flush();
              $session->setFlash('folderActionMessage', 'Folder '. $folder->getName(). " successfully deleted.");
            }
          }
        }
      }
      else {  // folder not found
        $session->setFlash('folderActionMessage', "Folder with id $folderId not found.");
      }
      return $this->redirect($this->generateUrl('manageFolders'));
    }    

    // -------------------------------------------------------------------------
    
    public function deleteAccountImageAction($type) {
        $account = $this->get('security.context')->getToken()->getUser();
        $this->deleteOldFiles('/data/accounts/'.$type, $account->getId().'\.*');
        return $this->redirect($this->generateUrl('manageAccount'));            
    }
    
    // -------------------------------------------------------------------------
    
    public function uploadImageAction(Request $request, $type, $formClass, $account) {			
        $form = $this->createForm($formClass);
        $message = '';
    
        if ($request->getMethod() == 'POST' && isset($_POST[$formClass->getName()])) {
            $form->bindRequest($request);
            $data = $form->getData();
            if ($form->isValid() && $data['file']) {
              // odstraneni puvodniho souboru
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

    private function resizeImage($imageName, $width = 0, $height = 0) {
        list($width_orig, $height_orig) = getimagesize($imageName);
		if($width_orig > 500){
			$width = 500;
			$height = ($height_orig * $width) / $width_orig;
		} else {
			$width = $width_orig;
			$height = $height_orig;			
		}
/*
        $original = imagecreatefromjpeg($imageName);
        $resampled = imagecreatetruecolor($width, $height);

        imagecopyresampled($resampled, $original, 0, 0, 0, 0, $width, $height, $width_orig,  $height_orig);
        imagejpeg($resampled, $imageName, 100);

        imagedestroy($original);
        imagedestroy($resampled);
		*/
    }
                     
}
                                                          