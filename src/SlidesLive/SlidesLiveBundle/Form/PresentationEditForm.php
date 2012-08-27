<?php

namespace Meta\MetaBundle\Form;

use Meta\MetaBundle\Form\SimpleForm;
use Symfony\Component\Form\FormBuilder;

class PresentationEditForm extends SimpleForm {

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($field_name, $field_type, $label,Boolean $required, Array $other_params); */     
    $this->add('title', 'text', 'Title:');
    $this->add('description', 'textarea', 'Description:');
    $this->add('lang', 'text', 'Language:', true);
    
  }
  
  public function getName () {
    return 'presentationEdit';
  }
  
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'Meta\MetaBundle\Entity\Presentation',
        'validation_groups' => array('edit'),
    );
  }

}

 