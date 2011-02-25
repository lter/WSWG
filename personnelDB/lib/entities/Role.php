<?php

namespace PersonnelDB;
use \DOMElement as DOMElement;

require_once('Entity.php');

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

  public function getIdentity() {
    return $this->storeFront->IdentityStore->getById($this->personID);
  }

  public function getRoleType() {
    return $this->storeFront->RoleTypeStore->getById($this->roleTypeID, $this->type);
  }

  public function getSite() {
    return $this->storeFront->SiteStore->getById($this->siteID);
  }

  public function setSiteByAcronym($acronym) {
    // find site by acronym
    // set siteID to site->id
  }

  /* Serialization */
  // returns a representation of itself as an xml fragment that conforms to the personelDB.xsd 
  public function to_xml_fragment() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('role'));
    $xml_obj->setAttribute('type',$this->getRoleType()->roleType);
    $xml_obj->appendChild($xml_doc->createElement('roleID',$this->roleID ));
    $xml_obj->appendChild($xml_doc->createElement('roleType',$this->roleType ));
    $site = $this->getSite();
    $xml_obj->appendChild($xml_doc->createElement('siteAcronym',$site->siteAcronym ));
    $xml_obj->appendChild($xml_doc->createElement('beginDate',$this->beginDate ));
    $xml_obj->appendChild($xml_doc->createElement('endDate',$this->endDate ));
    $xml_obj->appendChild($xml_doc->createElement('isActive',$this->isActive ));

    return $xml_obj;
  }
  public function to_xml() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('person'));
    $xml_obj->appendChild($xml_doc->createElement('personID', $this->personID));
    $fragment = $xml_doc->importNode($this->to_xml_fragment(), TRUE);
    $xml_obj->appendChild($xml_doc->createElement('roleList'))->appendChild($fragment);
    return $xml_doc;
  }


  public function from_xml($xml_dom) {
    if ($xml_dom->nodeName == 'role')
      throw new \Exception('role->from_xml can only deal with role nodes');
    
    $xpath = new \DOMXPath($xml_dom);
    $this->roleID = $xpath.query("*/roleID/")->nodeValue;
    
    $role_fragment = $xpath.query("*/roleType/");
    $this->roleTypeID = $this->storeFront->RoleTypeStore->getByFilter(array('roleType' => $role_fragment->nodeValue),($role_fragment->getAttribute('type')));

    $this->siteID = $this->storeFront->SiteStore->getByFilter(array('siteAcronym' => $xpath.query("*/siteAcronym/")->nodeValue));

    $this->beginDate = $xpath.query("*/beginDate/")->nodeValue;
    $this->endDate = $xpath.query("*/endDate/")->nodeValue;
    $this->isActive = $xpath.query("*/isActive/")->nodeValue;
  }
}
