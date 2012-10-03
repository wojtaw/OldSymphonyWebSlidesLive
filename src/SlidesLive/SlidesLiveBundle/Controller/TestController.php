<?php

namespace SlidesLive\SlidesLiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\Collection;

class TestController extends Controller {

    protected $data = array();                                         
                                                  
    public function linkBrowserAction($accountCanName) {
      $account = $this->getDoctrine()->getRepository('SlidesLiveBundle:Account')
        ->findByCanonicalName($accountCanName);
      if (count($account) != 1) {
        $this->data['account'] = null;
      }
      else {
        $this->data['account'] = $account[0];
      }      
      return $this->render('SlidesLiveBundle:Test:linkBrowser.html.twig', $this->data);   
    }

    public function passwordChangeAction($accountCanName, $password) {
      $em = $this->getDoctrine()->getEntityManager();
      $results = $em->getRepository('SlidesLiveBundle:Account')->findByCanonicalName($accountCanName);
      if (count($results) != 1) {
        print("Account not found.<br />\n");        
      }
      else {
        $account = $results[0];
        print("Accont: ".$account->getName()."<br />\n");
        print("Password: $password<br />\n");
        $encoder = $this->get('security.encoder_factory')->getEncoder($account);
        $account->setPassword($encoder->encodePassword($password, $account->getSalt()));
        print("Password created.<br />\n");                
        $em->flush();
        print("Password saved.<br />\n");        
      }
      return new Response('');
    }
    
    public function formStyleAction() {
      $request = $this->getRequest();
      $validations = new Collection(array(
          'text' => array(
            new MinLength(5),
            new NotBlank(),
            new NotNull()
          ),
          'readonly' => array(),
          'email' => array(),
          'password' => array(),
          'textarea' => array(),
          'hidden' => array(),
          'choice' => array(),
          'checkbox' => array(),
          'repeated' => array(),
      ));

      $this->data['form'] = $this->createFormBuilder(null, array(
        'validation_constraint' => $validations
        ))
        ->add('text', 'text', array (
            'label' => 'Textove pole:',
            'required' => false
          ))
        ->add('readonly', 'text', array (
            'label' => 'Jen pro cteni:',
            'data' => 'Sem se psat neda!',
            'read_only' => true,
            'max_length' => 50,
            'attr' => array(
                'style' => 'color: red;',
              ),
            'required' => false
          ))
        ->add('email', 'email', array (
            'label' => 'Email:',
            //'max_length' => 10,
            'required' => null
          ))
        ->add('password', 'password', array (
            'label' => 'Heslo:',
            'required' => false
          ))
        ->add('textarea', 'textarea', array (
            'label' => 'TextArea:',
            'data' => 'Dlouhy text ... ... ...',
            'trim' => true,
            'required' => false
          ))
        ->add('choice', 'choice', array (
            'label' => 'Moznosti:',
            'choices' => array(
                '1' => 'jedna',
                '2' => 'dva',
                '3' => 'tri',
              ),
            'empty_value' => false,
            'required' => false
          ))
        ->add('checkbox', 'checkbox', array (
            'label' => 'CheckBox:',
            'value' => 'OK',
            'attr' => array(
                'checked' => 1
              ),
            'required' => false
          ))
        ->add('repeated', 'repeated', array (
            'label' => 'Opakovaci policko:',
            'type' => 'text',
            'first_name' => 'Prvni:',
            'second_name' => 'Druhe:',
            'options' => array(
                'max_length' => 5,
              ),
            'required' => false
          ))
        ->add('hidden', 'hidden', array (
            'label' => 'Skryte Policko',
            'data' => 'skryte policko'
        ))
      ->getForm();

      if ($request->getMethod() == 'POST') {
        $this->data['form']->bindRequest($request);
        $this->data['form']->isValid();
      }

      $this->data['form'] = $this->data['form']->createView();
      return $this->render('SlidesLiveBundle:Test:formStyle.html.twig', $this->data);
    }                     
}
