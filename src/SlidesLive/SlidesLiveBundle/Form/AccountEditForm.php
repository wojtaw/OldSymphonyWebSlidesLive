<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use SlidesLive\SlidesLiveBundle\Form\SimpleForm;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

class AccountEditForm extends SimpleForm {

  protected $account;
  protected $accountFolders = array();

  public function __construct($account) {
    $this->account = $account;
    foreach($account->getFolders() as $folder) {
      $this->accountFolders[$folder->getId()] = $folder->getName();
    }
  }

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($field_name, $field_type, $label,Boolean $required, Array $other_params); */     
    $this->add('username', 'email', 'Email:', true);
    $this->add('description', 'textarea', 'Description of your account:', false);
    $this->add('website', 'text', 'Your website:', false);
    $this->add('showHeader', 'checkbox', 'Show header navigation bar:', false);
    $this->add('showFooter', 'checkbox', 'Show footer navigation bar:', false);
    $this->add('privacy', 'choice', 'Account visibility:', false, array(
      'choices' => array (
        Privacy::P_PUBLIC => 'Public', 
        Privacy::P_UNLISTED => 'Unlisted',
        Privacy::P_PRIVATE => 'Private'
      ),
      'empty_value' =>false,
    ));
    $builder->add('old_password', 'password', array('property_path' => false));
    $builder->add('new_password', 'repeated', array(
        'type' => 'password',
        'invalid_message' => 'The password fields must match.',
        'first_name' => 'New_Password:',
        'second_name' => 'New_Password_again:',
        'property_path' => false,
      )
    );
    //$builder->add('primaryFolder', new FolderSelectionForm($this->account));
    $this->add('primaryFolderId', 'choice', 'Folder:', false, array(
      'choices' => $this->accountFolders,
      'empty_value' =>false,
      'property_path' => false,
      'data' => $this->account->getPrimaryFolder()->getId(),
    ));
  }
  
  public function getName () {
    return 'accountEdit';
  }
  
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'SlidesLive\SlidesLiveBundle\Entity\Account',
    );
  }
  
  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $constraintsCollection = new Collection(
      array(
        'old_password' => new NotBlank(array('message' => 'This value should not by blank.')), 
        'new_password' => array(
          new MinLength(array('limit' => 6, 'message' => 'Password must be longer then 6 characters.')),
          new NotBlank(array('message' => 'This value should not by blank.'))
        )
      )
    );
    
    $resolver->setDefaults(array(
      'validation_constraint' => $constraintsCollection
    ));
  }

}

 