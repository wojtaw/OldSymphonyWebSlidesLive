<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use SlidesLive\SlidesLiveBundle\Form\SimpleForm;
use Symfony\Component\Form\FormBuilder;

class FolderSelectionForm extends SimpleForm {

  protected $accountFolders = array();

  public function __construct($account) {
    foreach($account->getFolders() as $folder) {
      $this->accountFolders[$folder->getCanonicalName()] = $folder->getName();
    }
  }

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($field_name, $field_type, $label,Boolean $required, Array $other_params); */     
    $this->add('canonicalName', 'choice', 'Folder:', false, array(
      'choices' => $this->accountFolders,
      'empty_value' =>false,
    ));
    
  }
  
  public function getName () {
    return 'folderSelection';
  }
  
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'SlidesLive\SlidesLiveBundle\Entity\Folder',
    );
  }

}

 