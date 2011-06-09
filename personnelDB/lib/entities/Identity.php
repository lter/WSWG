<?php

namespace PersonnelDB;
use \DOMElement as DOMElement;

require_once('Entity.php');

class Identity extends Entity {

  /* MEMBER DATA */

  public $aliases = array();


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


  /* SERIALIZATION */

  // returns a representation of itself as an xml fragment that conforms to the personelDB.xsd 
  public function to_xml_fragment() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('identity'));

    $this->add_xml_if($xml_doc, $xml_obj, 'prefix');
    $xml_obj->appendChild($xml_doc->createElement('firstName', $this->firstName));
    $this->add_xml_if($xml_doc, $xml_obj, 'middleName');
    $xml_obj->appendChild($xml_doc->createElement('lastName', $this->lastName));
    $this->add_xml_if($xml_doc, $xml_obj, 'preferredName');
    $this->add_xml_if($xml_doc, $xml_obj, 'title');
    $xml_obj->appendChild($xml_doc->createElement('primaryEmail', $this->primaryEmail));
    $xml_obj->appendChild($xml_doc->createElement('optOut', $this->optOut));

    foreach($this->aliases as $alias) {
      $xml_obj->appendChild($xml_doc->createElement('nameAlias', $alias));
    }

    return $xml_obj;
  }

  public function from_xml_fragment($node) {
    if ($node->nodeName != 'identity')
      throw new \Exception('Identity->from_xml_fragment() can only deal with identity nodes');

    $xpath = new \DOMXPath($node->ownerDocument);

    $this->prefix = $this->get_xml_if($node, 'prefix');
    $this->firstName = $this->get_xml_if($node, 'firstName');
    $this->middleName = $this->get_xml_if($node, 'middleName');
    $this->lastName = $this->get_xml_if($node, 'lastName');
    $this->preferredName = $this->get_xml_if($node, 'preferredName');
    $this->title = $this->get_xml_if($node, 'title');
    $this->primaryEmail = $this->get_xml_if($node, 'primaryEmail');
    $this->optOut = $this->get_xml_if($node, 'optOut');

    foreach($xpath->query('nameAlias', $node) as $alias) {
      $this->aliases[] = $alias->nodeValue;
    }
  }

}
