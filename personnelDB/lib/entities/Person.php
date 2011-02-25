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

    foreach($xpath->query("*/roleList/role") as $role_element) {
      $role_xpath = new \DOMXPath($role_element);
      $node_id = $role_xpath.query("/roleID")->nodeValue;
      if ($node_id) {
        $role = $this->storeFront->RoleStore->getById($node_id);
      } else {
        $role = new Role();
      }
      $role->from_xml($role_element);
      $roles->push($role);
    }
    unset($role_element);

    foreach($xpath->query("*/contactInfoList/contact") as $contact_element) {
      $contact_xpath = new \DOMXPath($contact_element);
      $contact_id = $contact_xpath.query("/contactInfoID")->nodeValue;
      if ($contact_id){
        $contact = $this->storeFront->ContactInfoStore->getById($contact_id);
      } else {
        $contact = new Contact();
      }
      $contact->from_xml($contact_element);
      $contacts->pusth($contact);
    }
  }
}
