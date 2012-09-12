<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SimpleForm extends AbstractType {

  /**
   * @var FormBuilder $builder
   * 
   * Instance Formbuilderu využívaná metodou add()      
   */     
  protected $builder;
  
  /**
   * @fn add (field, type, label, required [, args])
   * @breif       
   * 
   * @param field - jméno formulářového pole odpovídající atributu entity, ke které se formulář vztahuje
   * @param type - typ formulářového políčka
   * @param label - popisek formulářového políčka
   * @param requred - true/false hodnota, určuje zda je políčko formuláře povinné
   * @param args - pole dalších argumentů formulářového pole                  
   */     
  protected function add($field, $type, $label, $required = false, $args = array()) {
    $this->builder->add($field, $type, array_merge(
                                            array('label' => $label, 'required' => $required),
                                            $args
                                            )
    );
  }

  /**
   *
   */     
  public function buildForm (FormBuilder $builder, array $options) {
    //$this->builder = $builder;
    
  }
  
  /**
   *
   */     
  public function getName () {
    return 'simpleform';
  }
  
  public function getDefaultOptions(array $options) {
    return array();
  }
       

}

 