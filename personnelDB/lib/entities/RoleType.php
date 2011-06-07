<?php

namespace PersonnelDB;

class RoleType extends Entity {

  /* METHODS */

  public function __construct($inf = null) {
    parent::__construct($inf);
  }

  public function destruct() {
    parent::__destruct();
  }


  /* RELATION METHODS */


  /* SERIALIZATION */
  public function to_xml_fragment() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('roleType'));

    $this->add_xml_if($xml_doc, $xml_obj, 'nsfRoleTypeID');
    $this->add_xml_if($xml_doc, $xml_obj, 'localRoleTypeID');
    $this->add_xml_if($xml_doc, $xml_obj, 'roleType');
    $this->add_xml_if($xml_doc, $xml_obj, 'type');
    $this->add_xml_if($xml_doc, $xml_obj, 'isRepeatable');

    return $xml_obj;
  }

  public function from_xml_fragment($node) {
    
  }
}