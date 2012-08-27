<?php

namespace Meta\MetaBundle\Entity\Other;

/**
 * Meta\MetaBundle\Entity\Other\AccessControl
 *
 */
class AccessControl {
  
  protected $entities;
  protected $privateLingAccess;
  
  public function __construct ($entity, $class = 'Channel') {
    $this->entities = array();
    $this->entities[$class] = $entity;
    $this->privateLinkAccess = false; 
  }
  
  public function getEntity($class = 'Channel') {
    if (isset($this->entities[$class])) {
      return $this->entities[$class];
    }
    else {
      return null;
    }
  }
  
  public function isPrivateLinkAccess() {
    return $this->privateLinkAccess;
  }
  
  public function setPrivateLinkAccess($boolean) {
    $this->privateLinkAccess = $boolean;    
  }
  
}