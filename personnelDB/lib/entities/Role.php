<?php

namespace PersonnelDB;

class Role extends Entity {

  /* METHODS */

  public function __construct($inf = null) {
    parent::__construct($inf);
  }

  public function destruct() {
    parent::__destruct();
  }


  /* RELATION METHODS */

  public function getPerson() { }

  public function getRoleType() { }

  public function getSite() { }

  /* Serialization */
  // returns a representation of itself as an xml fragment that conforms to the personelDB.xsd 
  public function to_xml() {
    $xml_obj = new DOMElement('role');
    $xml_obj->setAttribute('type',getNodeType()->roleType));
    $xml_obj->appendChild(new DOMElement('roleID',$this->roleID ));
    $xml_obj->appendChild(new DOMElement('roleType',$this->roleType ));
    $xml_obj->appendChild(new DOMElement('getSite()->siteAcronym',$this->getSite()->siteAcronym ));
    $xml_obj->appendChild(new DOMElement('beginDate',$this->beginDate ));
    $xml_obj->appendChild(new DOMElement('endDate',$this->endDate ));
    $xml_obj->appendChild(new DOMElement('isActive',$this->isActive ));

    return $xml_obj;
  }

  public function from_xml($xml_string) {

  }
}
