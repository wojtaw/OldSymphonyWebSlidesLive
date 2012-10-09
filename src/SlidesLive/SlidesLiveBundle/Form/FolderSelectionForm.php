<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use SlidesLive\SlidesLiveBundle\Form\SimpleForm;
use Symfony\Component\Form\FormBuilder;

class FolderSelectionForm extends SimpleForm {

  protected $accountFolders = array();
  protected $account = null;

  public function __construct($account) {
    $this->account = $account;
    foreach($account->getFolders() as $folder) {
      $this->accountFolders[$folder->getId()] = $folder->getName();
    }
  }

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($field_name, $field_type, $label,Boolean $required, Array $other_params); */     
    $this->add('folderId', 'choice', 'Folder:', false, array(
      'choices' => $this->accountFolders,
      'empty_value' =>false,
      'property_path' => false,
      'selectedchoice' => $this->account->getPrimaryFolder();
    ));
    
  }
  
  public function getName () {
    return 'folderSelection';
  }
  
  public function getDefaultOptions(array $options) {
    return array();
  }

}

 