<?php

namespace PersonnelDB;
use \DOMElement as DOMElement;

require_once('Entity.php');

class Identity extends Entity {

  /* MEMBER DATA */

  private $aliases = array();


  /* METHODS */

  public function __construct($inf = null) {
    parent::__construct($inf);

    // Populate aliases
    $this->aliases = $this->storeFront->IdentityStore->getAliases($this->personID);
  }

  public function destruct() {
    parent::__destruct();
  }


  /* RELATION METHODS */

  public function getPerson() { }

 /* Serialization */
  // returns a representation of itself as an xml fragment that conforms to the personelDB.xsd 

  public function to_xml() {
    $xml_obj = new DOMElement('identity');
    $xml_obj->appendChild(new DOMElement('prefix',$this->prefix ));
    $xml_obj->appendChild(new DOMElement('firstName',$this->firstName ));
    $xml_obj->appendChild(new DOMElement('middleName',$this->middleName ));
    $xml_obj->appendChild(new DOMElement('lastName',$this->lastName ));
    $xml_obj->appendChild(new DOMElement('preferredName',$this->preferredName ));
    $xml_obj->appendChild(new DOMElement('title',$this->title ));
    $xml_obj->appendChild(new DOMElement('optOut',$this->optOut ));
    if (aliases.length > 0){
      $xml_obj->appendChild(new DOMElement('aliases',$this->aliases ));
    }
    return $xml_obj;
  }

  public function from_xml($xml_dom) {
    if ($xml_dom->nodeName == 'identity')
      throw new \Exception('Identity->from_xml can only deal with identity nodes');
     }
    $xpath = new \DOMXPath($xml_dom);
    $this->prefix = $xpath.query("*/prefix/")->nodeValue;
    $this->firstName = $xpath.query("*/firstName/")->nodeValue;
    $this->middleName = $xpath.query("*/middleName/")->nodeValue;
    $this->lastName = $xpath.query("*/lastName/")->nodeValue;
    $this->title = $xpath.query("*/title/")->nodeValue;
    $this->primaryEmail = $xpath.query("*/primaryEmail/")->nodeValue;
    $this->optOut = $xpath.query("*/optOut/")->nodeValue;
    $this->nameAlias = $xpath.query("*/nameAlias/")->nodeValue;
  }

}
