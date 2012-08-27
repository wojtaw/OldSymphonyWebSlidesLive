<?php

namespace Meta\MetaBundle\Form;

use Meta\MetaBundle\Form\SimpleForm;
use Symfony\Component\Form\FormBuilder;

class PresentationAppPersistForm extends SimpleForm {

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($field_name, $field_type, $label[, Boolean $required[, Array $other_params]]); */     
    $this->add('title_cs',        'text',     'title_cs:');
    $this->add('title_en',        'text',     'title_en:');
    $this->add('description_cs',  'textarea', 'description_cs:');
    $this->add('description_en',  'textarea', 'description_en:');
    $this->add('lang',            'text',     'lang:', true);
    $this->add('dateRecorded',    'integer',  'dateRecorded:', true);
    $this->add('service',         'text',     'service:', true);
    $this->add('service_id',      'text',     'service_id', true);
    $this->add('length',          'integer',  'length', true);
    $this->add('slides',          'integer',  'slides', true);
    $this->add('video',           'integer',  'video', true);
    
  }
  
  public function getName () {
    return 'presentationAppPersist';
  }
  
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'Meta\MetaBundle\Entity\Presentation',
    );
  }

}

 