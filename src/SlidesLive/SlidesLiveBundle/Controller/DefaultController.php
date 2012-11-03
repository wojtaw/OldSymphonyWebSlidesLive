<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SlidesLive\SlidesLiveBundle\Controller\BaseController;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;

/*
 TODOs
  - rozsirit folderAction a presentationAction o zpracovani unlisted modu - FIX
  - vyresit generovani ruznych odkazu z twigu podle toho v jakem se nachazi stranka modu
  - radne otestovat
  
  -> pokud se povede prevest do jednoho kontroleru
  -> registrovat seznam folderu jako widget - pouzivat jako sluzbu
*/

class DefaultController extends Controller { 

  protected $data;
  protected $privacyLevel = Privacy::P_PUBLIC;
  
  public function __construct() {
    $this->data = array(
      'privacyLevel' => $this->privacyLevel,
      'stylesheet' => null,       // individualni styl accountu
      'account' => null,      
      'presentation' => null,     // prezentace pro prehrávac  
      'folders' => array(),       // seznam folderu kanálu
      'folderPresentations' => array(),   // prezentace aktuálního folderu
    );
  }
  
  /**
   * Route: /{accountCanName}
   * Zobrazeni obsahu vybraneho uctu. Zobrazi se primarni slozka accountu.
   * Pokud je uzivatel prihlasen, vidi kompletni obsah sveho uctu.      
   */     
  public function accountAction($accountCanName, $hash = null) {
    // nacteni accountu
    $this->data['account'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Account')
      ->findAccount($accountCanName);
    if (!$this->data['account']) {  // zadany account neexistuje -> presmerovani na error page
      return $this->accountNotFound($accountCanName);
    }
    $context = $this->get('security.context');
    if ($context->isGranted('ROLE_USER')) {
      if ($context->getToken()->getUser()->getId() == $this->data['account']->getId()) { // uzivatel je prihlasen a diva se na svuj kanal -> zobrazi se vsechno
        $this->privacyLevel = Privacy::P_PRIVATE; // muze se zobrazit vsechno
      }      
    }
    // overeni zda se user nenachazi v UNLISTED modu
    if (!$this->checkUnlistedMode($hash, $this->data['account'])) {
      return $this->accountNotFound($accountCanName);    
    }    
    // neprihlaseny uzivatel -> Privacy omezeni
    if ($this->data['account']->getPrivacy() > $this->privacyLevel) { // uzivatel se diva na private nebo unlisted account -> je presmerovan pryc.
      return $this->accountNotFound($accountCanName);      
    }
// ---   
    // zjisteni viditelnych folderu podle privacyLevelu
    $this->data['folders'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Folder')
      ->findAccountFolders($this->data['account']->getId(), $this->privacyLevel);
    if (count($this->data['folders']) == 0) { // nenalezeny zadne public foldery -> vykresleni prazdneho accountu
      return $this->render('SlidesLiveBundle:Default:playerPage.html.twig', $this->data);  
    }
    // nacteni folderu k vypsani
    $folderToView = $this->data['account']->getPrimaryFolder();
    if ($folderToView->getPrivacy() > $this->privacyLevel) { // primarni folder neni public -> nahrazani prvnim folderem v seznamu
      $folderToView = $this->data['folders'][0];  // vybere se jedna viditelna slozka
    }
// ---
    // nacteni prezentaci slozky, ktera je zobrazena
    $this->data['folderPresentations'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Presentation')
      ->findFolderPresentations($folderToView->getId(), $this->privacyLevel);
    if (count($this->data['folderPresentations']) == 0) {
      return $this->render('SlidesLiveBundle:Default:playerPage.html.twig', $this->data);
    }
    // nacteni prezentace do prehravace
    $this->data['presentation'] = $this->data['folderPresentations'][0];       

    return $this->render('SlidesLiveBundle:Default:playerPage.html.twig', $this->data);
  }
  
  /**
   * Route: /{accountCanName}/{folderCanName}
   * Zobrazeni obsahu zadane slozky zadaneho uctu. Pokud se uzivatel diva na
   * vlastni ucet a je prihlasen, zobrazi se vsechny prezentace a foldery.      
   */     
  public function folderAction($accountCanName, $folderCanName, $hash = null) {
    // nacteni accountu
    $this->data['account'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Account')
      ->findAccount($accountCanName);
    if (!$this->data['account']) {  // zadany account neexistuje -> presmerovani na error page
      return $this->accountNotFound($accountCanName);
    }
    // rozhodnuti jestli je user prihlaseny a diva se na svuj kanal
    $context = $this->get('security.context');
    if ($context->isGranted('ROLE_USER')) {
      if ($context->getToken()->getUser()->getId() == $this->data['account']->getId()) { // uzivatel je prihlasen a diva se na svuj kanal
        $this->privacyLevel = Privacy::P_PRIVATE; // muze se zobrazit vsechno
      }      
    } 
    // neprihlaseny uzivatel -> Privacy omezeni
    if ($this->data['account']->getPrivacy() > $this->privacyLevel) { // uzivatel se diva na private nebo unlisted account -> je presmerovan pryc.
      return $this->accountNotFound($accountCanName);      
    }
 // ---   
    // overeni nazvu folderu a zda ji uzivatel muze zobrazit v privacyLevel modu
    $folderToView = $this->getDoctrine()->getRepository('SlidesLiveBundle:Folder')
        ->findAccountFolder($this->data['account']->getId(), $folderCanName, $this->privacyLevel);
    if (!$folderToView) {                                                       // ??? nebo by se mohla zprava zobrazit na playerPage, aby si user mohl hned vybrat jinou slozku
      return $this->folderNotFound($folderCanName);
    }
    // overeni zda se user nenachazi v UNLISTED modu
    if (!$this->checkUnlistedMode($hash, $folderToView)) {
      return $this->folderNotFound($folderCanName);    
    }
    // zjisteni viditelnych folderu podle privacyLevelu
    $this->data['folders'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Folder')
      ->findAccountFolders($this->data['account']->getId(), $this->privacyLevel);
    if (count($this->data['folders']) == 0) { // nenalezeny zadne public foldery -> vykresleni prazdneho accountu
      return $this->render('SlidesLiveBundle:Default:playerPage.html.twig', $this->data);  
    }
// ---
    // nacteni prezentaci slozky, ktera ma byt zobrazena
    $this->data['folderPresentations'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Presentation')
      ->findFolderPresentations($folderToView->getId(), $this->privacyLevel);
    if (count($this->data['folderPresentations']) == 0) {
      return $this->render('SlidesLiveBundle:Default:playerPage.html.twig', $this->data);
    }
    // nacteni prezentace do prehravace
    $this->data['presentation'] = $this->data['folderPresentations'][0];       
          
    return $this->render('SlidesLiveBundle:Default:playerPage.html.twig', $this->data);
  }
  
  public function presentationAction($accountCanName, $folderCanName, $presentationId, $hash = null) {
    // nacteni prezentace
    $presentation = $this->getDoctrine()->getRepository('SlidesLiveBundle:Presentation')->find($presentationId);
    if (!$presentation) {   // prezentace nebyla podle zadaneho Id a privateLevel nalezena
        return $this->presentationNotFound($presentationId);  
    }
    // nacteni accountu    
    $account = $presentation->getAccount();       
    if ($accountCanName != null) {  // kanonicke jmeno accountu bylo zadano -> overeni nalezitosti k prezentaci
      if ($accountCanName != $account->getCanonicalName()) {      // pokud se kan. jmeno zadaneho accountu nerovna kan. jmenu accountu prezentace -> redirect
        return $this->presentationNotFound($presentationId);
      }            
    }
    // nacteni folderu
    $folder = $presentation->getFolder();
    if ($folderCanName != null) { // kononicke jmeno folderu bylo zadano -> overeni nalezitosti k prezentaci
      if ($folderCanName != $folder->getCanonicalName()) {      // zadane kanonicke folderu neodpovida kan. jmenu folderu prezentace
        return $this->presentationNotFound($presentationId);      
      }      
    }
    // zjisteni zda je uzivatel prihlasena a diva se na svuj ucet
    $context = $this->get('security.context');
    if ($context->isGranted('ROLE_USER')) {
      if ($context->getToken()->getUser() == $account) {
        $this->privacyLevel = Privacy::P_PRIVATE; // prihlaseny user se diva na svuj ucet -> muze se zobrazit vsechno      
      }
    }
    // overeni zda se user nenachazi v UNLISTED modu
    if (!$this->checkUnlistedMode($hash, $presentation)) {
      return $this->presentationNotFound($presentationId);    
    }
    // overeni zda jsou nactene entity viditelne v privacyLevelu
    if ($account->getPrivacy() > $this->privacyLevel
      || $folder->getPrivacy() > $this->privacyLevel
      || $presentation->getPrivacy() > $this->privacyLevel) {
      return $this->presentationNotFound($presentationId);      
    }
    // nacteni folderu vybraneho accountu
    $this->data['folders'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Folder')
      ->findAccountFolders($account->getId(), $this->privacyLevel);
    // nacteni prezentaci slozky, ktera ma byt zobrazena
    $this->data['folderPresentations'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Presentation')
      ->findFolderPresentations($folder->getId(), $this->privacyLevel);
    
    $this->data['presentation'] = $presentation;
    $this->data['account'] = $account;          
    return $this->render('SlidesLiveBundle:Default:playerPage.html.twig', $this->data);
  }
  
  public function searchAction() {      
    return $this->render('SlidesLiveBundle:Default:searchResults.html.twig');
  } 
  
  public function categoryAction($categoryName){
    $this->data['categoryName'] = $categoryName;
	
    $categoryId = $this->getDoctrine()->getRepository('SlidesLiveBundle:Category')
      ->findCategoryIdAccordingName($categoryName);	
	

    $this->data['presentations'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Category')
      ->findPresentations($categoryId->getId());
    if (!$this->data['presentations']) {
      //TO DO
    } else {
		
	}
		 	  
    return $this->render('SlidesLiveBundle:Default:categoryPage.html.twig', $this->data);	  
  }
  
// ----------------HELP METHODS-------------------------------------------------

  protected function accountNotFound($accountCanName) {
    return $this->redirect($this->generateUrl('errorPage', array('message' => "We are sorry, but '$accountCanName' account does not exist or is private.")));
  }
  
  protected function folderNotFound($folderCanName) {
    return $this->redirect($this->generateUrl('errorPage', array('message' => "We are sorry, but '$folderCanName' folder in '" . $this->data['account']->getName() . "' account does not exist or is private.")));
  }
  
  protected function presentationNotFound($presentationId) {
    return $this->redirect($this->generateUrl('errorPage', array('message' => "We are sorry, but presentation with id '$presentationId' does not exist or is private.")));
  }
  
  /**
   * Kontrola pokud je pristupovano pres UNLISTED link (= byl zadan hash), zda
   * je hash a link spravny. Pokud ano, je privacyLevel nastaven na UNLISTED.   
   * Pokud je uzivatel prihlasen a diva se na svuj kanal zustava privacyLevel
   * na PRIVATE.
   * @return true - pokud je link v poradku, false - pokud ne a musi se zobrazit chyba.      
   */        
  protected function checkUnlistedMode($hash, $entity) {
    if ($hash != null) {        // byl zadan hash = pristup pres UNLISTED link -> kontrola hashe (samotny link musi byt spravne aby se zobrazila player page)
      if ($hash == $entity->getHash()) {       // hash sedi
        if ($this->privacyLevel == Privacy::P_PUBLIC) {     // pokud se prihlaseny uzivatel nediva na svuj ucet (PRIVATE mod), tak povolime UNLISTED prezentace
          $this->privacyLevel = Privacy::P_UNLISTED;
        }
      }
      else {  // hash nesedi -> spatny link
        return false;      
      }    
    }
    return true;
  }
  
  protected function includeStylesheet(Account $account) {
      
  }
                                                                  
}

/*
// nacteni accountu
    $this->data['account'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Account')
      ->findAccount($accountCanName);
    if (!$this->data['account']) {  // zadany account neexistuje -> presmerovani na error page
      return $this->accountNotFound($accountCanName);
    }
    // rozhodnuti jestli je user prihlaseny a diva se na svuj kanal
    $context = $this->get('security.context');
    if ($context->isGranted('ROLE_USER')) {
      if ($context->getToken()->getUser()->getId() == $this->data['account']->getId()) { // uzivatel je prihlasen a diva se na svuj kanal
        $this->privacyLevel = Privacy::P_PRIVATE; // muze se zobrazit vsechno
      }      
    }
    // neprihlaseny uzivatel -> Privacy omezeni
    if ($this->data['account']->getPrivacy() > $this->privacyLevel) { // uzivatel se diva na private nebo unlisted account -> je presmerovan pryc.
      return $this->accountNotFound($accountCanName);      
    }
 // ---   
    // overeni nazvu folderu a zda ji uzivatel muze zobrazit v privacyLevel modu
    $folderToView = $this->getDoctrine()->getRepository('SlidesLiveBundle:Folder')
        ->findAccountFolder($this->data['account']->getId(), $folderCanName, $this->privacyLevel);
    if (!$folderToView) {                                                       // ??? nebo by se mohla zprava zobrazit na playerPage, aby si user mohl hned vybrat jinou slozku
      return $this->folderNotFound($folderCanName);
    }
    // nalezeni vybrane prezentace podle ID, jmena uctu a slozky
    $this->data['presentation'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Presentation')
      ->findPresentationByAccountAndFolder($this->data['account']->getId(), $folderToView->getId(), $presentationId);
    if (!$this->data['presentation']) {
      return $this->presentationNotFound($accountCanName);
    }
    // zjisteni viditelnych folderu podle privacyLevelu
    $this->data['folders'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Folder')
      ->findAccountFolders($this->data['account']->getId(), $this->privacyLevel);     // vzdy je nalezena alespon jedna slozka (folderToView)
// ---
    // nacteni prezentaci slozky, ktera ma byt zobrazena
    $this->data['folderPresentations'] = $this->getDoctrine()->getRepository('SlidesLiveBundle:Presentation')
      ->findFolderPresentations($folderToView->getId(), $this->privacyLevel);         // vzdy je nalezena alespon jedna prezentace (zadana prezentace)  
          
    return $this->render('SlidesLiveBundle:Default:playerPage.html.twig', $this->data);
*/