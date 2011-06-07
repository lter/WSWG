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

  public function getSite() {
    return $this->storeFront->SiteStore->getByID($this->siteID);
  }


  /* SERIALIZATION */
  public function to_xml_fragment() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('roleType'));

    switch ($this->type) {
    case 'nsf':
      $xml_obj->appendChild($xml_doc->createElement('nsfRoleTypeID', $this->nsfRoleTypeID));
      $xml_obj->appendChild($xml_doc->createElement('roleName', $this->roleName));
      $xml_obj->appendChild($xml_doc->createElement('type', $this->type));
      $xml_obj->appendChild($xml_doc->createElement('isRepeatable', $this->isRepeatable));
      break;

    case 'local':
      $xml_obj->appendChild($xml_doc->createElement('localRoleTypeID', $this->localRoleTypeID));
      $xml_obj->appendChild($xml_doc->createElement('roleName', $this->roleName));
      $xml_obj->appendChild($xml_doc->createElement('type', $this->type));
      $xml_obj->appendChild($xml_doc->createElement('isRepeatable', $this->isRepeatable));
      $xml_obj->appendChild($xml_doc->createElement('siteAcronym', $this->getSite()->siteAcronym));
      break;
    }

    return $xml_obj;
  }

  public function from_xml_fragment($node) {
    if ($node->nodeName != 'roleType')
      throw new \Exception('roleType->from_xml_fragment() can only deal with roleType nodes');

    $xpath = new \DOMXPath($node->ownerDocument);
    $this->type = $xpath->evaluate("type", $node)->item(0)->nodeValue;

    switch ($this->type) {
    case 'nsf':
      $this->nsfRoleTypeID = $xpath->query("nsfRoleTypeID")->nodeValue;
      $this->roleName = $xpath->query("roleName")->nodeValue;
      $this->isRepeatable = $xpath->query("isRepeatable")->nodeValue;
      break;
      
    case 'local':
      $this->localRoleTypeID = $this->get_xml_if($node, 'localRoleTypeID');
      $this->roleName = $this->get_xml_if($node, 'roleName');
      $this->isRepeatable = $this->get_xml_if($node, 'isRepeatable');

      $siteAcronym = $this->get_xml_if($node, 'siteAcronym');
      $sites = $this->storeFront->SiteStore->getByFilter(array('siteAcronym' => $siteAcronym));
      $this->siteID = $sites[0]->siteID;
      break;
    }
  }
}