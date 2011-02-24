<?php

namespace PersonnelDB;

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

  // returns a representation of itself as an xml string that conforms to the personelDB.xsd 
  public function to_xml() {
    $this->xml_obj = new DOMElement('person');
    $this->person_id = new DOMElement('personID');
    $this->xml_obj->appendChild(new DOMElement('personID',getIdentity()->personID));
    $this->xml_obj->appendChild(getIdentity()->to_xml());
    if (getRoles()->length > 0) {
      $this->xml_obj->appendChild(getRoles()->to_xml());
    }
    if (getContactInfo()->length > 0) {
      $this->xml_obj->appendChild(getContactInfo());
    }
    return $this->xml_obj;
  }

  public function from_xml() {

  }
}
