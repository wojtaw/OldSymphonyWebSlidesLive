<?php

namespace SlidesLive\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

use SlidesLive\SlidesLiveBundle\Entity\Account;
use SlidesLive\SlidesLiveBundle\Entity\Folder;
use SlidesLive\SlidesLiveBundle\Entity\Presentation;
use SlidesLive\AppBundle\Form\Type\AccountType;

class DefaultController extends Controller
{
    public function createAccountAction()
    {
        $request = $this->getRequest();

        $account = new Account();

        $form = $this->createForm(new AccountType(), $account);
        $form->bindRequest($request);

        if ($form->isValid())
        {
            $canonicalName = $account->canonizeName();
            $errors = $this->get('validator')->validate($account);
            if (count($errors) > 0)
            {
                foreach ($errors as $error)
                {
                    if ($error->getPropertyPath() === "canonicalName")
                        $form["name"]->addError(new FormError($error->getMessage()));
                    else
                        $form[$error->getPropertyPath()]->addError(new FormError($error->getMessage()));
                }
            }
            else
            {
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

                $response = new Response();
                $response->setStatusCode(201);
                return $response;
            }
        }

        return View::create($form, 400);
    }

    /**
     * @Rest\View
     */
    public function newsAction()
    {
        return array(
            "version" => 1,
            "messageTitle" => "SlidesLive",
            "message" => "",
        );
    }

    /**
     * @Rest\View
     */
    public function indexAction()
    {
        return array();
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
