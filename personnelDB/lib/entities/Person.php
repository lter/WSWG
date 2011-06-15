<?php

namespace PersonnelDB;
use \DOMElement as DOMElement;

require_once('Entity.php');

class Person extends Entity {

  /* MEMBER DATA */

  public $roles = array();
  public $contacts = array();


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
    if ($this->personID)
      return $this->storeFront->RoleStore->getByFilter(array('personID' => $this->personID));
    else
      return null;
  }

  public function getContactInfo() {
    if ($this->personID)
      return $this->storeFront->ContactInfoStore->getByFilter(array('personID' => $this->personID));
    else
      return null;
  }


  /* SERIALIZATION */

  // returns a representation of itself as an xml fragment that conforms to the personnelDB.xsd 
  public function to_xml_fragment() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('person'));
    $xml_obj->appendChild($xml_doc->createElement('personID', $this->personID));

    if (!$identity = $this->getIdentity())
      $identity = $this->storeFront->IdentityStore->getEmpty();

    $fragment = $xml_doc->importNode($identity->to_xml_fragment(), TRUE);
    $xml_obj->appendChild($fragment);

    $role_xml = $xml_obj->appendChild(new DOMElement('roleList'));
    if ($roles = $this->getRoles()) {
      foreach($roles as $role) {
	$fragment = $xml_doc->importNode($role->to_xml_fragment(), TRUE);
	$role_xml->appendChild($fragment);
      }
    }
    
    $contact_xml = $xml_obj->appendChild(new DOMElement('contactInfoList'));
    if ($contacts = $this->getContactInfo()) {
      foreach($contacts as $contact) {
	$fragment = $xml_doc->importNode($contact->to_xml_fragment(), TRUE);
	$contact_xml->appendChild($fragment);
      }
    }

    return $xml_obj;
  }

  public function from_xml_fragment($node) {
    if ($node->nodeName != 'person') {
      throw new \Exception('person->from_xml_fragment can only deal with person nodes');
    }

    $xpath = new \DOMXPath($node->ownerDocument);
    $this->personID = $this->get_xml_if($node, 'personID');

    // Untransmute component parts
    $identity = $xpath->query('identity', $node)->item(0);
    $this->identity = $this->storeFront->IdentityStore->getEmpty();
    $this->identity->from_xml_fragment($identity);
    $this->identity->personID = $this->personID;

    $this->roles = array();
    foreach($xpath->query('roleList/role', $node) as $role_element) {
      $role = $this->storeFront->RoleStore->getEmpty();
      $role->from_xml_fragment($role_element);
      $role->personID = $this->personID;
      $this->roles[] = $role;
    }

    $this->contacts = array();
    foreach($xpath->query('contactInfoList/contactInfo', $node) as $contact_element) {
      $contact = $this->storeFront->ContactInfoStore->getEmpty();
      $contact->from_xml_fragment($contact_element);
      $contact->personID = $this->personID;
      $this->contacts[] = $contact;
    }
  }

}
