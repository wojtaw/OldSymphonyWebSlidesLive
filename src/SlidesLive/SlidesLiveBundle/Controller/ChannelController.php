<?php

namespace Meta\MetaBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Meta\MetaBundle\Controller\BaseController;
use Meta\MetaBundle\Entity\Channel;
use Meta\MetaBundle\Entity\Presentation;

use Symfony\Component\HttpFoundation\Response;


class ChannelController extends BaseController
{

    public function __construct() {
      parent::__construct();
    }

    public function channelsAction() {
        $this->start();
        $em = $this->getDoctrine()->getEntityManager();
        $session = $this->get('session');

        $this->data['channels'] = $em->getRepository('MetaBundle:Channel')->listAllChannels($session->get('accessControl'));
        // Kontrola zda jsou v DB vubec ulozeny nejake kanaly
        if (count($this->data['channels']) < 1) {
            $this->data['channels'] = null; 
        }
        return $this->render('MetaBundle:Channel:channels.html.twig', $this->data);
    }
    
    public function channelPageAction ($channelName,$videoId, $player = "auto") {
        $this->start();
        $em = $this->getDoctrine()->getEntityManager();
        $session = $this->get('session');
        $this->data = array( 'channel' => null,
                             'folder' => null, 
                             'selectedPresentation' => null,      
                             'folderPresentations' => null,
                             'stylesheet' => null,
                             'player' => $player
                             );
        
        $this->data['channel'] = $em->getRepository('MetaBundle:Channel')->findOneChannelByCanonicalName($channelName, $session->get('accessControl')); 
        // Kontrola jmena kanalu, pokud kanal neexistuje -> presmerovani na stranku kanalu + flashMessage               
        if (empty($this->data['channel'])) {
            $session = $this->getRequest()->getSession();
            $session->setFlash('notice', "Channel with canonical name '$channelName' does not exist or is not public!");
            return $this->redirect($this->generateUrl('errorPage'));    
        }
        
        //Naloadovani vlastnich CSS stylu kanalu
        if (file_exists('./bundles/meta/channels/css/'.$this->data['channel']->getId().'.css')) {
          $this->data['stylesheet'] = './bundles/meta/channels/css/'.$this->data['channel']->getId().'.css'; 
        }
         
        // if (videoId == -1) { getFolders, getContent of PrimaryFolder }
        // else { look for specified presentation }
        // if (exists) { show it and its folder }
        // else { redirect ot errorPage }
        
        if ($videoId == -1) { // odpovida route /channels/mujKanal
          if($this->data['channel']->getPrimaryFolder() == null) {  // channel nema nastavenou primaryFolder - to je hodne spatne
            //return $this->render('MetaBundle:Channel:channelPage.html.twig', $this->data);
            $session->setFlash('notice', 'Channels primary folder is unavailable.');
            return $this->redirect($this->generateUrl('errorPage'));                        
          }
          $this->data['folder'] = $this->data['channel']->getPrimaryFolder();
// SORT
//          $this->data['folderPresentations'] = $this->data['folder']->getPresentations();
          $this->data['folderPresentations'] = $em->getRepository('MetaBundle:Presentation')->findFolderPresentationsOrdered($this->data['folder']);
          if (count($this->data['folderPresentations']) > 0) {
            $this->data['selectedPresentation'] = $this->data['folderPresentations'][0];
          }
          return $this->render('MetaBundle:Channel:channelPage.html.twig', $this->data);
        }
        else {
          $selectedPresentation = $em->getRepository('MetaBundle:Presentation')->findAuthorizedPresentationById($videoId, $session->get('ac'));
          if (is_null($selectedPresentation)) { // prezentace se zadanym id neexistuje
            $session->setFlash('notice', 'We are sorry but, presentation with this id does not exist or is not public.');
            return $this->redirect($this->generateUrl('errorPage'));
          }
          else if ($selectedPresentation->getChannel()->getId() != $this->data['channel']->getId()) {  // prezentace nalezena, ale nenalezi kanalu zvolenemu v URL
            $session->setFlash('notice', 'We are sorry but, presentation with this id does not belong to channels specified in URL.');
            return $this->redirect($this->generateUrl('errorPage'));
          }
          else { // prezentace nalezena HUR�
            $this->data['selectedPresentation'] = $selectedPresentation;
            $this->data['folder'] = $selectedPresentation->getFolder();
// SORT
//            $this->data['folderPresentations'] = $this->data['folder']->getPresentations();
            $this->data['folderPresentations'] = $em->getRepository('MetaBundle:Presentation')->findFolderPresentationsOrdered($this->data['folder']);
          }                  
        }
        
        
        return $this->render('MetaBundle:Channel:channelPage.html.twig', $this->data);            
    }
    
    public function folderPageAction($channelName, $folderName, $player = "auto") {
        $this->start();
        $em = $this->getDoctrine()->getEntityManager();
        $session = $this->get('session');
        $this->data = array( 'channel' => null,
                             'folder' => null, 
                             'selectedPresentation' => null,     
                             'folderPresentations' => array(),
                             'stylesheet' => null,
                             'player' => $player
                             );
        
        $this->data['channel'] = $em->getRepository('MetaBundle:Channel')->findOneChannelByCanonicalName($channelName, $session->get('accessControl')); 
        // Kontrola jmena kanalu, pokud kanal neexistuje -> presmerovani na stranku kanalu + flashMessage               
        if (empty($this->data['channel'])) {
            $session = $this->getRequest()->getSession();
            $session->setFlash('notice', "Channel with canonical name '$channelName' does not exist or is not public!");
            return $this->redirect($this->generateUrl('errorPage'));    
        }
        
        //Naloadovani vlastnich CSS stylu kanalu
        if (file_exists('./bundles/meta/channels/css/'.$this->data['channel']->getId().'.css')) {
          $this->data['stylesheet'] = './bundles/meta/channels/css/'.$this->data['channel']->getId().'.css'; 
        }
        
        if ($folderName == null) {
          $folderName = $this->data['channel']->getPrimaryFolder()->getName();
        }
        
        $folder = null;
        foreach ($this->data['channel']->getFolders() as $f) {
           if ($f->getCanonicalName() == $folderName) {
              $folder = $f;
              break;
           }        
        } 
        if ($folder == null) {
          $session->setFlash('notice', 'Folder with this name does not exist in this folder.');
          return $this->redirect($this->generateUrl('errorPage'));        	
        }
        $this->data['folder'] = $folder;
      
        $folderPresentations = $em->getRepository('MetaBundle:Folder')->findPresentationsSortedByDate($folder->getId());
        $folderPresentations = $folder->getPresentations();
        if (count($folderPresentations) > 0) {
          $this->data['folderPresentations'] = $folderPresentations;
          $this->data['selectedPresentation'] = $folderPresentations[0];  
        }
    
        return $this->render('MetaBundle:Channel:channelPage.html.twig', $this->data);
        
    }
}

                                                      