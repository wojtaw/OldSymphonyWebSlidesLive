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
use SlidesLive\AppBundle\Form\Type\PresentationType;

class DefaultController extends Controller
{
    public function createAccountAction(Request $request)
    {
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
            "version" => 2,
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

    public function createPresentationAction(Request $request) {
        $presentation = new Presentation();
        $em = $this->getDoctrine()->getEntityManager();
        $account = $this->get('security.context')->getToken()->getUser();
        $folder = $account->getPrimaryFolder();

        // otevreni logu
        $log = fopen('./account-log.txt', 'a');
        fwrite($log, date("- H:i:s - d.m.Y:\n"));
        fwrite($log, "\tSTART:                account: " . $account->getUsername() . ", password: " . $account->getPassword() . "\n");

        $form = $this->createForm(new PresentationType(), $presentation);
        $form->bindRequest($request);

        if ($form->isValid())
        {
            $account->addPresentation($presentation);
            $presentation->setAccount($account);

            $folder->addPresentation($presentation);
            $presentation->setFolder($folder);

            fwrite($log, "\tPRESENTATION ADDED:   account: " . $account->getUsername() . ", password: " . $account->getPassword() . "\n");
            $em->persist($presentation);
            $em->flush();
            fwrite($log, "\tPERSISTED:            account: " . $account->getUsername() . ", password: " . $account->getPassword() . "\n");

            fclose($log);
            return View::create(
                array(
                    "presentation.id" => $presentation->getId(),
                    "http.host" => "virtual.edumeta.com",
                    "http.port" => 80,
                    "http.user" => "meta",
                    "http.password" => "6u5tVEwKzQ",

                    "ftp.host" => "77.93.223.217",
                    "ftp.directory" => "/data/presentationQueue",
                    "ftp.user" => "meta",
                    "ftp.password" => "6u5tVEwKzQ",
                ), 201);
        }
        else {
            fwrite($log, "\tINVALID FORM DATA!\n");
        }

//         return array("aaa" => new \DateTime("2012-09-09"));
//         return ;
        fclose($log);
        return View::create($form, 400);
    }

}
