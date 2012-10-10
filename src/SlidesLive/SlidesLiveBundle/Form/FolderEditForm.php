<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use SlidesLive\SlidesLiveBundle\Form\SimpleForm;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Choice;
use SlidesLive\SlidesLiveBundle\Form\Type\PrivacyChoiceType;

class FolderEditForm extends SimpleForm {

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($field_name, $field_type, $label,Boolean $required, Array $other_params); */     
    $this->add('name', 'text', 'Name:', false);
    $this->add('privacy', new PrivacyChoiceType(), 'Folder visibility:', false, array(
      'empty_value' =>false,
    ));
  }
  
  public function getName () {
    return 'folderEdit';
  }
  
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'SlidesLive\SlidesLiveBundle\Entity\Folder',
    );
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $constraintsCollection = new Collection(
      array(
        'name' => new NotNull(array('message' => 'This value should not be blank.')), 
        'privacy' => array(
          new Choice(array('choices' => Privacy::getValues())),
          new NotBlank(),
        )
      )
    );
    
    $resolver->setDefaults(array(
      'validation_constraint' => $constraintsCollection,
    ));
  }

}

 