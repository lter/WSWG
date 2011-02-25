<?php

namespace PersonnelDB;
use \DOMElement as DOMElement;

require_once('Entity.php');

class Person extends Entity {

  /* METHODS */

  public function __construct($inf = null) {
    parent::__construct($inf);
  }

  public function destruct() {
    parent::__destruct();
  }


  /* RELATION METHODS */

  public function getIdentity() {
    return $this->storeFront->IdentityStore->getById($this->personID);
  }

  public function setIdentity() { }

  public function getRoles() {
    return $this->storeFront->RoleStore->getByFilter(array('personID' => $this->personID));
  }

  public function setRoles() { }

  public function getContactInfo() {
    return $this->storeFront->ContactInfoStore->getByFilter(array('personID' => $this->personID));
  }

  public function setContactInfo() { }

  // returns a representation of itself as an xml fragment that conforms to the personelDB.xsd 
  public function to_xml() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('person'));
    $xml_obj->appendChild($xml_doc->createElement('personID', $this->personID));
    $xml_obj->appendChild($this->getIdentity()->to_xml_fragment());
    if ($this->roles) {
      $role_xml = $xml_obj->appendChild(new DOMElement('roleList'));
      foreach($this->roles as $role) {
        $role_xml->appendChild($role->to_xml_fragment());
      }
    }
    if ($this->contactInfo) {
      $contact_xml = $xml_obj->appendChild(new DOMElement('contactInfoList'));
      foreach($this->contactInfo as $contact) {
        $contact_xml->appendChild($contact->to_xml_fragment());
      }
    }
    return $xml_doc;
  }

  public function from_xml($xml_dom) {
    if ($xml_dom->nodeName != 'person') {
      throw new \Exception('person->from_xml can only deal with person nodes');

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
      $role->personID = $this->personID;
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
      $contact->personID = $this->personID;
      $contacts->push($contact);
    }
    unset($contact_elment);
  }
}
