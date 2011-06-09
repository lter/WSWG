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

  public function getPerson() {
    return $this->storeFront->PersonStore->getByID($this->personID);
  }

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


  /* SERIALIZATION */

  // returns a representation of itself as an xml fragment that conforms to the personelDB.xsd 
  public function to_xml_fragment() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('role'));
    $xml_obj->appendChild($xml_doc->createElement('roleID', $this->roleID));
    $xml_obj->appendChild($xml_doc->createElement('isActive', $this->isActive));

    $roleType = $this->getRoleType();
    $rt = $xml_obj->appendChild($xml_doc->createElement('roleType', $roleType->roleName));
    $rt->setAttribute('type', $roleType->type);

    $site = $this->getSite();
    $xml_obj->appendChild($xml_doc->createElement('siteAcronym', $site->siteAcronym));

    $this->add_xml_if($xml_doc, $xml_obj, 'beginDate');
    $this->add_xml_if($xml_doc, $xml_obj, 'endDate');

    return $xml_obj;
  }

  public function from_xml_fragment($node) {
    if ($node->nodeName != 'role')
      throw new \Exception('Role->from_xml_fragment() can only deal with role nodes');
    
    $xpath = new \DOMXPath($node->ownerDocument);
    $this->roleID = $this->get_xml_if($node, 'roleID');
    $this->type = $this->get_xml_if($node, 'roleType/@type');
    $this->isActive = $this->get_xml_if($node, 'isActive');
    $this->beginDate = $this->get_xml_if($node, 'beginDate');
    $this->endDate = $this->get_xml_if($node, 'endDate');
    
    $roleName = $this->get_xml_if($node, 'roleType');
    $roleTypes = $this->storeFront->RoleTypeStore->getByFilter(array('roleName' => $roleName), $this->type);
    $this->roleTypeID = $this->type == 'nsf' ? $roleTypes[0]->nsfRoleTypeID : $roleTypes[0]->localRoleTypeID;

    $siteAcronym = $this->get_xml_if($node, 'siteAcronym');
    $sites = $this->storeFront->SiteStore->getByFilter(array('siteAcronym' => $siteAcronym));
    $this->siteID = $sites[0]->siteID;
  }
}
