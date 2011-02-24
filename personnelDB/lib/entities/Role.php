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
  public function to_xml() {
    $this->xml_obj = new DOMElement('role');
    $this->xml_obj->setAttribute('type',getNodeType()->roleType);
    $this->xml_obj->appendChild(new DOMElement('roleID'),$this->roleID );
    $this->xml_obj->appendChild(new DOMElement('roleType'),$this->roleType );
    $this->xml_obj->appendChild(new DOMElement('getSite()->siteAcronym'),$this->getSite()->siteAcronym );
    $this->xml_obj->appendChild(new DOMElement('beginDate'),$this->beginDate );
    $this->xml_obj->appendChild(new DOMElement('endDate'),$this->endDate );
    $this->xml_obj->appendChild(new DOMElement('isActive'),$this->isActive );

    return $this->xml_obj;
  }

  public function from_xml() {

  }
}
