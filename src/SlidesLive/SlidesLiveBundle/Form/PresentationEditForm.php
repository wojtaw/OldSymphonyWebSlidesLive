<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use SlidesLive\SlidesLiveBundle\Form\SimpleForm;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;
use Symfony\Component\Form\FormBuilder;
use SlidesLive\SlidesLiveBundle\DependencyInjection\LanguageList;

class PresentationEditForm extends SimpleForm {

  protected $account;
  protected $presentation;
  protected $accountFolders = array();

  public function __construct($account, $presentation) {
    $this->presentation = $presentation;
    $this->account = $account;
    foreach($account->getFolders() as $folder) {
      $this->accountFolders[$folder->getId()] = $folder->getName();
    }
  }

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($field_name, $field_type, $label,Boolean $required, Array $other_params); */     
    $this->add('title', 'text', 'Title:');
    $this->add('description', 'textarea', 'Description:');
    $this->add('lang', 'choice', 'Language:', true, array (
        'choices' => LanguageList::getLanguages(),
        'empty_value' => false,
      )
    );
    $this->add('privacy', 'choice', 'Presentation visibility:', false, array(
      'choices' => array (
        Privacy::P_PUBLIC => 'Public', 
        Privacy::P_UNLISTED => 'Unlisted',
        Privacy::P_PRIVATE => 'Private'
      ),
      'empty_value' =>false,
    ));
    $this->add('folder', 'choice', 'Folder:', false, array(
      'choices' => $this->accountFolders,
      'empty_value' =>false,
      'property_path' => false,
      'data' => $this->presentation->getFolder()->getId(),
    ));
    $this->add('externalMedia', 'text', 'Youtube link:');
  }
  
  public function getName () {
    return 'presentationEdit';
  }
  
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'SlidesLive\SlidesLiveBundle\Entity\Presentation',
        'validation_groups' => array('edit'),
        'cascade_validation' => true,
    );
  }

}

 