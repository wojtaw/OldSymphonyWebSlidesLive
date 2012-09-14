<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use SlidesLive\SlidesLiveBundle\Form\SimpleForm;
use Symfony\Component\Form\FormBuilder;

class RegistrationForm extends SimpleForm {

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($username, $field_type, $label,Boolean $required, Array $other_params); */     
    $this->add('username', 'email', 'Email:', true);
    //$builder->add('channel', new ChannelRegistrationForm());
    $this->add('name', 'text', 'Account name:', true);
    $this->add('canonicalName', 'hidden', 'canonicalName', false);

    $builder->add('password', 'repeated', array (
      'type' => 'password',
      //'required' => true,
      'invalid_message' => 'The password fields must match.',
      //'label' => 'Password:',
      'first_name' => 'Password:',
      'second_name' => 'Password again:'
      //'options' => array (
        //'label' => 'Password:',
        //'required' => true
      //  )
      )
    );
    
  }
  
  public function getName () {
    return 'registration';
  }
  
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'SlidesLive\SlidesLiveBundle\Entity\Account',
    );
  }

}

 