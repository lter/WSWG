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
    $this->xml_obj.appendChild($this->xml_obj->createElement('prefix'), )
    $this->xml_obj.appendChild($this->xml_obj->createElement('personID',getIdentity()->personID));
    $this->xml_obj.appendChild(getIdentity()->to_xml());
    if (getRoles()->length > 0) {
      $this->xml_obj.appendChild(getRoles()->to_xml());
    }
    if (getContactInfo()->length > 0) {
      $this->xml_obj.appendChild(getContactInfo());
    }
  }

  public function from_xml() {}
  }
