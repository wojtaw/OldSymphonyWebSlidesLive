<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use SlidesLive\SlidesLiveBundle\Form\SimpleForm;
use Symfony\Component\Form\FormBuilder;

class Form extends SimpleForm {

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($field_name, $field_type, $label[, Boolean $required[, Array $other_params]]); */     
    $this->add('', '', '');
    
  }
  
  public function getName () {
    return '';
  }
  
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'SlidesLive\SlidesLiveBundle\Entity\',
    );
  }

}

 