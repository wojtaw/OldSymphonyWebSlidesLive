<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use SlidesLive\SlidesLiveBundle\Form\SimpleForm;
use Symfony\Component\Form\FormBuilder;

class ChannelRegistrationForm extends SimpleForm {

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($username, $field_type, $label,Boolean $required, Array $other_params); */     
    $this->add('name', 'text', 'Account name:', true);
    $this->add('canonicalName', 'hidden', 'canonicalName', false);
    
  }
  
  public function getName () {
    return 'channel_registration';
  }
  
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'SlidesLive\SlidesLiveBundle\Entity\Channel',
    );
  }

}

 