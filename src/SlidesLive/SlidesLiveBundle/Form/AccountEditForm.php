<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use SlidesLive\SlidesLiveBundle\Form\SimpleForm;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;
use Symfony\Component\Form\FormBuilder;

class AccountEditForm extends SimpleForm {

  public function buildForm (FormBuilder $builder, array $options) {
    $this->builder = $builder;
    
    /* $this->add($field_name, $field_type, $label,Boolean $required, Array $other_params); */     
    $this->add('email', 'email', 'Email:', true);
    $this->add('description', 'textarea', 'Channel Description:', false);
    $this->add('purpose', 'textarea', 'Purpose:', true);
    //$this->add('private', 'checkbox', 'Private:', false);
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
    
  }
  
  public function getName () {
    return 'accountEdit';
  }
  
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'SlidesLive\SlidesLiveBundle\Entity\Account',
    );
  }

}

 