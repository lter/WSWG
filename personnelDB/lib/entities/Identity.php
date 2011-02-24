<?php

namespace PersonnelDB;

// Include abstract parent class
require_once('Entity.php');


class Identity extends Entity {

  /* MEMBER DATA */

  private $aliases = array();


  /* METHODS */

  public function __construct($inf = null) {
    parent::__construct($inf);

    // Populate aliases
    $this->aliases = $this->storefront->IdentityStore->getAliases($this->personID);
  }

  public function destruct() {
    parent::__destruct();
  }


  /* RELATION METHODS */

  public function getPerson() { }

}

 /* Serialization */

  public function to_xml() {
    $this->xml_obj = new DOMElement('identity');
    $this->xml_obj->appendChild(new DOMElement('prefix'),$this->prefix );
    $this->xml_obj->appendChild(new DOMElement('firstName'),$this->firstName );
    $this->xml_obj->appendChild(new DOMElement('middleName'),$this->middleName );
    $this->xml_obj->appendChild(new DOMElement('lastName'),$this->lastName );
    $this->xml_obj->appendChild(new DOMElement('preferredName'),$this->preferredName );
    $this->xml_obj->appendChild(new DOMElement('title'),$this->title );
    $this->xml_obj->appendChild(new DOMElement('optOut'),$this->optOut );
    if (aliases.length > 0){
      $this->xml_obj->appendChild(new DOMElement('aliases'),$this->aliases );
    }
    return $this->xml_obj;
  }

  public function from_xml() {}
  }
