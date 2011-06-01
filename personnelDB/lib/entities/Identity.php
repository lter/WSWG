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

  public function getPerson() {
    return $this->storeFront->PersonStore->getByID($this->personID);
  }

  /* Serialization */
  // returns a representation of itself as an xml fragment that conforms to the personelDB.xsd 
  public function to_xml_fragment() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('identity'));
    $this->add_xml_if($xml_doc, $xml_obj, 'prefix');
    $this->add_xml_if($xml_doc, $xml_obj, 'firstName');
    $this->add_xml_if($xml_doc, $xml_obj, 'middleName');
    $this->add_xml_if($xml_doc, $xml_obj, 'lastName');
    $this->add_xml_if($xml_doc, $xml_obj, 'preferredName');
    $this->add_xml_if($xml_doc, $xml_obj, 'title');
    $this->add_xml_if($xml_doc, $xml_obj, 'primaryEmail');
    $this->add_xml_if($xml_doc, $xml_obj, 'optOut');
    foreach($this->aliases as $alias) {
      $xml_obj->appendChild($xml_doc->createElement('aliases',$alias));
    }
    return $xml_obj;
  }

  public function to_xml() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('person'));
    $xml_obj->appendChild($xml_doc->createElement('personID', $this->personID));
    $fragment = $xml_doc->importNode($this->to_xml_fragment(), TRUE);
    $xml_obj->appendChild($fragment);

    return $xml_obj;
  }


  public function from_xml($xml_dom) {
    if ($xml_dom->nodeName == 'identity')
      throw new \Exception('Identity->from_xml can only deal with identity nodes');

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
