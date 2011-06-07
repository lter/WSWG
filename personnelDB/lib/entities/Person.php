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

  public function getRoles() {
    return $this->storeFront->RoleStore->getByFilter(array('personID' => $this->personID));
  }

  public function getContactInfo() {
    return $this->storeFront->ContactInfoStore->getByFilter(array('personID' => $this->personID));
  }


  /* SERIALIZATION */

  // returns a representation of itself as an xml fragment that conforms to the personnelDB.xsd 
  public function to_xml_fragment() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('person'));
    $xml_obj->appendChild($xml_doc->createElement('personID', $this->personID));

    $fragment = $xml_doc->importNode($this->getIdentity()->to_xml_fragment(), TRUE);
    $xml_obj->appendChild($fragment);

    if ($roles = $this->getRoles()) {
      $role_xml = $xml_obj->appendChild(new DOMElement('roleList'));
      foreach($roles as $role) {
	$fragment = $xml_doc->importNode($role->to_xml_fragment(), TRUE);
	$role_xml->appendChild($fragment);
      }
    }
    
    if ($contacts = $this->getContactInfo()) {
      $contact_xml = $xml_obj->appendChild(new DOMElement('contactInfoList'));
      foreach($contacts as $contact) {
	$fragment = $xml_doc->importNode($contact->to_xml_fragment(), TRUE);
	$contact_xml->appendChild($fragment);
      }
    }

    return $xml_obj;
  }

  public function from_xml_fragment($node) {
    if ($xml_dom->nodeName != 'person') {
      throw new \Exception('person->from_xml_fragment can only deal with person nodes');
    }

    $xpath = new \DOMXPath($xml_dom);
    $this->personID = $xpath.query("*/personID/")->nodeValue;
    $roles = array();

    // Untransmute component parts
    $this->identity = $this->storeFront->IdentityStore->getEmpty();
    $this->identity->from_xml_fragment();

    $this->roles = array();
    foreach($xpath->query("*/roleList/role") as $role_element) {
      $role = $this->storeFront->RoleStore->getEmpty();
      $role->from_xml_fragment($role_element);
      $role->personID = $this->personID;
      $this->roles[] = $role;
    }

    $this->contacts = array();
    foreach($xpath->query("*/contactInfoList/contact") as $contact_element) {
      $contact = $this->storeFront->ContactInfoStore->getEmpty();
      $contact->from_xml_fragment($contact_element);
      $contact->personID = $this->personID;
      $this->contacts[] = $contact;
    }
  }

}
