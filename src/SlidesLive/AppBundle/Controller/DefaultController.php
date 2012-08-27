<?php

namespace SlidesLive\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use SlidesLive\SlidesLiveBundle\Entity\Presentation;

class DefaultController extends Controller
{                                         
    protected $data;
    
    public function indexAction() {
        return new Response(json_encode(array(
            "messageTitle" => "Meta Edu Studio",
            "message" => "",
        )));
    }

    public function newsAction() {
        return new Response(json_encode(array(
            "version" => 1,
            "messageTitle" => "Meta Edu Studio",
            "message" => "",
        )));
    }
    
    private function returnOK($presentationId) {
     return new Response(json_encode(array(
           "presentation.id" => $presentationId,
           "ftp.host" => "77.93.223.217",
           "ftp.directory" => "/data/presentationQueue",
           "ftp.user" => "meta",
           "ftp.password" => "6u5tVEwKzQ",
     )));
   }
    
    private function returnError($code) {
      return new Response(json_encode(array(
        "error" => $code
      )));    
    }

    public function createPresentationAction(Request $request) {
        $this->start();
        $channel = $this->get('security.context')->getToken()->getUser()->getChannel();
        
        if (
          $request->getMethod() == 'POST'
          && $request->request->has('title')
          && $request->request->has('description')
          && $request->request->has('lang')
          && $request->request->has('dateRecorded')
          && $request->request->has('service')
          && $request->request->has('service_id')
          && $request->request->has('length')
          && $request->request->has('slides')
          && $request->request->has('video')
        ) {
        
          $presentation = new Presentation();
          
          $presentation->setTitle($request->request->get('title'));
          $presentation->setDescription($request->request->get('description'));
          $presentation->setLang($request->request->get('lang'));
          $presentation->setService($request->request->get('service'));
          $presentation->setServiceId($request->request->get('service_id'));
          $presentation->setLength($request->request->get('length'));
          $presentation->setslides($request->request->get('slides'));
          $presentation->setvideo($request->request->get('video'));
          $presentation->setShowSpeaker(0);
          
          $presentation->setDateRecorded(date_timestamp_set(date_create(), $request->request->get('dateRecorded')));
          $presentation->setChannel($channel);
          $folder = $presentation->getChannel()->getPrimaryFolder(); 
          $presentation->setFolder($folder);
          $folder->addPresentation($presentation);          
        
          try {
              
              $errors = $this->get('validator')->validate($presentation);
              if (count($errors) < 1) {                  
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($presentation);
                $em->flush();          
                return $this->returnOK($presentation->getId());
              }
              else {
                $errorMessages = '';
                foreach ($errors as $e) {
                  $errorMessages .= " " . $e->getPropertyPath() . " : " . $e->getMessage() . "\n<br />";
                }
                return $this->returnError('The presentation data are not valid.'.$errorMessages);
              }
    
          }
          catch ( \Exception $e) {
            throw $e;
            return $this->returnError('Exception thrown when putting data into database.');
          }
              
        }
                
        return $this->returnError('No POST data.');
    }
                     
}
