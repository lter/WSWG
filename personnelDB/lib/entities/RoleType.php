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

    $xml_obj->appendChild($xml_doc->createElement('roleName', $this->roleName));
    $xml_obj->appendChild($xml_doc->createElement('type', $this->type));
    $xml_obj->appendChild($xml_doc->createElement('isRepeatable', $this->isRepeatable));

    return $xml_obj;
  }

  public function from_xml_fragment($node) {
    if ($node->nodeName != 'roleType')
      throw new \Exception('roleType->from_xml_fragment() can only deal with roleType nodes');

    $xpath = new \DOMXPath($node->ownerDocument);
    $this->type = $xpath->evaluate("type");

    switch ($this->type) {
    case 'nsf':
      $this->nsfRoleTypeID = $xpath->query("nsfRoleTypeID")->nodeValue;
      $this->roleName = $xpath->query("roleName")->nodeValue;
      $this->isRepeatable = $xpath->query("isRepeatable")->nodeValue;
      break;
      
    case 'local':
      $this->localRoleTypeID = $xpath->query("localRoleTypeID")->nodeValue;
      $this->roleName = $xpath->query("roleName")->nodeValue;
      $this->isRepeatable = $xpath->query("isRepeatable")->nodeValue;

      $siteAcronym = $xpath->query("siteAcronym")->nodeValue;
      $sites = $this->storeFront->SiteStore->getByFilter(array('siteAcronym' => $siteAcronym));
      $this->siteID = $sites[0]->siteID;
      break;
    }
  }
}