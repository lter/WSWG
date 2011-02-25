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
  public function to_xml() {
    $xml_obj = new DOMElement('person');
    $xml_obj->appenChild(new DOMElement('personID'), $this->personID);
    $xml_obj->appenChild(new DOMElement('roleList'))->appendChild($this->to_xml_fragment());
    return $xml_obj;
  }

  // returns a representation of itself as an xml fragment that conforms to the personelDB.xsd 
  public function to_xml_fragment() {
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

  public function from_xml($xml_dom) {
    if ($xml_dom->nodeName == 'role')
      throw new \Exception('role->from_xml can only deal with role nodes');
     }
    
    $xpath = new \DOMXPath($xml_dom);
    $this->roleID = $xpath.query("*/roleID/")->nodeValue;
    
    $role_fragment = $xpath.query("*/roleType/");
    $this->roleTypeID = $this->storeFront->RoleTypeStore->getByFilter(('roleType'=>$role_fragment->nodeValue),($role_fragment->getAttribute('type'))

    $this->siteID = $this->storeFront->SiteStore->getByFilter(('siteAcronym'=>$xpath.query("*/siteAcronym/")->nodeValue));

    $this->beginDate = $xpath.query("*/beginDate/")->nodeValue;
    $this->endDate = $xpath.query("*/endDate/")->nodeValue;
    $this->isActive = $xpath.query("*/isActive/")->nodeValue;
  }
}
