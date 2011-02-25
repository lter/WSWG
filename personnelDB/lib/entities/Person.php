<?php

namespace PersonnelDB;
use \DOMElement as DOMElement;

class Person extends Entity {

  /* METHODS */

  public function __construct($inf = null) {
    parent::__construct($inf);
  }

  public function destruct() {
    parent::__destruct();
  }


  /* RELATION METHODS */

  public function getIdentity() { }

  public function getRoles() { }

  public function getContactInfo() { }

  // returns a representation of itself as an xml fragment that conforms to the personelDB.xsd 
  public function to_xml() {
    $xml_obj = new DOMElement('person');
    $xml_obj->appendChild(new DOMElement('personID',getIdentity()->personID));
    $xml_obj->appendChild(getIdentity()->to_xml());
    if (getRoles()->length > 0) {
      $xml_obj->appendChild(getRoles()->to_xml());
    }
    if (getContactInfo()->length > 0) {
      $xml_obj->appendChild(getContactInfo());
    }
    return $xml_obj;
  }

  public function from_xml($xml_string) {

  }
}
