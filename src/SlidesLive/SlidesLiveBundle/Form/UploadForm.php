<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use SlidesLive\SlidesLiveBundle\Form\SimpleForm;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Collection;


class UploadForm extends SimpleForm {
  
  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;    
    /* $this->add($field_name, $field_type, $label[, Boolean $required[, Array $other_params]]); */     
    $this->add('file', 'file', 'Select file:', false, array('property_path' => null));    
  }
  
  public function getName () {
    return 'upload_form';
  }
  
  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $constraintsCollection = new Collection(
      array(
        'file' => new File(
          array('maxSize' => '10M',)
        )
      )
    );
    
    $resolver->setDefaults(array(
      'validation_constraint' => $constraintsCollection
    ));
}
  
  
  public function getDefaultOptions(array $options) {
    return array();
  }
  

}

 