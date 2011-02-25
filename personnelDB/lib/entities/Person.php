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

  public function setIdentity() { }

  public function getRoles() { }

  public function setRoles() { }

  public function getContactInfo() { }

  public function setContactInfo() { }

  // returns a representation of itself as an xml fragment that conforms to the personelDB.xsd 
  public function to_xml() {
    $xml_obj = new DOMElement('person');
    $xml_obj->appendChild(new DOMElement('personID',getIdentity()->personID));
    $xml_obj->appendChild(getIdentity()->to_xml());
    if (getRoles()->length > 0) {
      $xml_obj->appendChild(new DOMElement('roleList'))->appendChild(getRoles()->to_xml());
    }
    if (getContactInfo()->length > 0) {
      $xml_obj->appendChild(new DOMElement('contactInfoList'))->appendChild(getContactInfo());
    }
    return $xml_obj;
  }

  public function from_xml($xml_dom) {
    if ($xml_dom->nodeName == 'person')
      throw new \Exception('person->from_xml can only deal with person nodes');
     }
    $xpath = new \DOMXPath($xml_dom);
    $this->personID = $xpath.query("*/personID/")->nodeValue;
    $roles = array();
    foreach($xpath.query("*/roleList/role") as $role_element) {
      //TODO: this is not right
      $roles.push($role_element->nodeName);
    }
  }
}
