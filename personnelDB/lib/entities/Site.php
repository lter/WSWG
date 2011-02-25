<?php

namespace PersonnelDB;

class Site extends Entity {

  /* METHODS */

  public function __construct($inf = null) {
    parent::__construct($inf);
  }

  public function destruct() {
    parent::__destruct();
  }


  /* RELATION METHODS */

  public function getRoles() { }

  public function getContactInfo() { }

  /* serialization */
  public function to_xml_fragment() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('site'));
    $this->add_xml_if($xml_doc, $xml_obj, 'siteID');
    $this->add_xml_if($xml_doc, $xml_obj, 'site');
    $this->add_xml_if($xml_doc, $xml_obj, 'siteAcronym');
  }

}
