<?php

namespace PersonnelDB;
use \DOMElement as DOMElement;

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

  public function setSiteByAcronym($acronym) {
    // find site by acronym
    // set siteID to site->id
  }

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

  // TODO: need to figure out if it is a nsf or local role
  public function from_xml($xml_dom) {
    if ($xml_dom->nodeName == 'role')
      throw new \Exception('role->from_xml can only deal with role nodes');
     }
    
    $xpath = new \DOMXPath($xml_dom);
    $this->roleID = $xpath.query("*/roleID/")->nodeValue;
    $this->roleType = $xpath.query("*/roleType/")->nodeValue;
    $this->setSiteByAcronym($xpath.query("*/siteAcronym/")->nodeValue);

    $this->beginDate = $xpath.query("*/beginDate/")->nodeValue;
    $this->endDate = $xpath.query("*/endDate/")->nodeValue;
    $this->isActive = $xpath.query("*/isActive/")->nodeValue;
  }
}
