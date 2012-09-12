<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use SlidesLive\SlidesLiveBundle\Form\SimpleForm;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;
use Symfony\Component\Form\FormBuilder;

class PresentationEditForm extends SimpleForm {

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($field_name, $field_type, $label,Boolean $required, Array $other_params); */     
    $this->add('title', 'text', 'Title:');
    $this->add('description', 'textarea', 'Description:');
    $this->add('lang', 'text', 'Language:', true);
    $this->add('privacy', 'choice', 'Presentation visibility:', false, array(
      'choices' => array (
        Privacy::P_PUBLIC => 'Public', 
        Privacy::P_UNLISTED => 'Unlisted',
        Privacy::P_PRIVATE => 'Private'
      ),
      'empty_value' =>false,
    ));
    
  }
  
  public function getName () {
    return 'presentationEdit';
  }
  
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'SlidesLive\SlidesLiveBundle\Entity\Presentation',
        'validation_groups' => array('edit'),
    );
  }

}

 