<?php

namespace Meta\MetaBundle\Form;

use Meta\MetaBundle\Form\SimpleForm;
use Symfony\Component\Form\FormBuilder;

class ChannelEditForm extends SimpleForm {

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($field_name, $field_type, $label,Boolean $required, Array $other_params); */     
    $this->add('description', 'textarea', 'Channel Description:', false);
    $this->add('private', 'checkbox', 'Private:', false);
    
  }
  
  public function getName () {
    return 'channelEdit';
  }
  
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'Meta\MetaBundle\Entity\Channel',
    );
  }

}

 