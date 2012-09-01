<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SlidesLive\SlidesLiveBundle\Controller\BaseController;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;

class DefaultController extends Controller { 

  protected $data = array(
    //'privacy_mode' => Privacy::p_public(),
    'stylesheet' => null,       // individualni styl accountu
    'account' => null,      
    'presentation' => null,     // prezentace pro prehrávac
    'folders' => array(),       // seznam folderu kanálu
    'folderPresentations' => array(),   // prezentace aktuálního folderu
  );

  public function accountAction($accountCanName) {
    //return new Response('Controller: Default, Action: account,<br />$accountCanName: '.$accountCanName);
    $accountRepo = $this->getDoctrine()->getEntityManager()->getRepository('SlidesLiveBundle:Account');
    $this->data['account'] = $accountRepo->findByCanonicalName($accountCanName);
    if (!$this->data['account']) {
      return $this->accountNotFound($accountCanName);
    }
    $this->data['account'] = $this->data['account'][0];
    $context = $this->get('security.context');
    if ($context->isGranted('ROLE_USER')) {
      if ($context->getToken()->getUser()->getId() == $this->data['account']->getId()) { // uzivatel je prihlasen a diva se na svuj kanal -> zobrazi se vsechno
        $this->data['folders'] = $this->data['account']->getFolders();
        $this->data['folderPresentations'] = $this->data['account']->getPrimaryFolder()->getPresentations();
        if (count($this->data['folderPresentations']) > 0) {
          $this->data['presentation'] = $this->data['folderPresentations'][0];
        }
      }      
    }
    else { // neprihlaseny uzivatel
      if ($this->data['account']->getPrivacy() == Privacy::p_private()) { // uzivatel se diva na private account -> je presmerovan pryc.
        return $this->accountNotFound($accountCanName);      
      }
      // zjisteni public folderu
      $this->data['folders'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Folder')
        ->findAccountFolders($this->data['account']->getId(), Privacy::p_public());
      if (count($this->data['folders']) == 0) { // nenalezeny zadne public foldery -> vykresleni prazdneho accountu
        return $this->render('SlidesLiveBundle:Default:playerPage.html.twig', $this->data);  
      }
      // nacteni folderu k vypsani
      $folderToView = $this->data['account']->getPrimaryFolder();
      if ($folderToView->getPrivacy() != Privacy::p_public()) { // primarni folder neni public -> nahrazani prvnim folderem v seznamu
        $folderToView = $this->data['folders'][0];
      }
      // nacteni prezentaci slozky, ktera je zobrazena
      $this->data['folderPresentations'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Presentation')
        ->findFolderPresentations($folderToView->getId(), Privacy::p_public());
      if (count($this->data['folderPresentations']) == 0) {
        return $this->render('SlidesLiveBundle:Default:playerPage.html.twig', $this->data);
      }
      // nacteni prezentace do prehravace
      $this->data['presentation'] = $this->data['folderPresentations'][0];      
    } 

    return $this->render('SlidesLiveBundle:Default:playerPage.html.twig', $this->data);
  }
  
  public function folderAction($accountCanName, $folderCanName) {
    return new Response("Controller: Default, Action: folderAction,<br />\$accountCanName: $accountCanName,<br />\$folderCanName: $folderCanName"); 
    //return $this->render('');
  }
  
  public function presentationAction($accountCanName, $folderCanName, $presentationId) {
    return new Response("Controller:Default , Action: presentation,<br />\$accountCanName: $accountCanName,<br />\$folderCanName: $folderCanName,<br />\$presentationId: $presentationId"); 
    //return $this->render('');
  }
  
// ----------------HELP METHODS-------------------------------------------------

  protected function accountNotFound($accountCanName) {
    return $this->redirect($this->generateUrl('errorPage', array('message' => "We are sorry, but '$accountCanName' does not exist or is private.")));
  }
  
  protected function prepareFolderPresentations() {
    
  }
  
  protected function includeStylesheet(Account $account) {
      
  }
                                                                  
}

