<?php

namespace PersonnelDB;
use \DOMElement as DOMElement;

require_once('Entity.php');

class ContactInfo extends Entity {

  /* MEMBER DATA */

  public $fields = array();


  /* METHODS */

  public function __construct($inf = null) {
    parent::__construct($inf);

    // Populate contact info fields
    $this->fields = $this->storeFront->ContactInfoStore->getFields($this->contactInfoID);
  }

  public function destruct() {
    parent::__destruct();
  }


  /* RELATION METHODS */

  public function getPerson() {
    return $this->storeFront->PersonStore->getByID($this->personID);
  }

  public function getSite() {
    return $this->storeFront->SiteStore->getByID($this->siteID);
  }


  /* SERIALIZATION */

  // returns a representation of itself as an xml fragment that conforms to the personelDB.xsd 
  public function to_xml_fragment() {
    $xml_doc = new \DOMDocument('1.0','utf-8');
    $xml_obj = $xml_doc->appendChild($xml_doc->createElement('contactInfo'));
    
    $xml_obj->appendChild($xml_doc->createElement('contactInfoID', $this->contactInfoID));
    $xml_obj->appendChild($xml_doc->createElement('label', $this->label));
    $xml_obj->appendChild($xml_doc->createElement('isPrimary', $this->isPrimary));
    $xml_obj->appendChild($xml_doc->createElement('isActive', $this->isActive));

    $site = $this->getSite();
    $xml_obj->appendChild($xml_doc->createElement('siteAcronym', $site->siteAcronym));

    $this->add_xml_if($xml_doc, $xml_obj, 'beginDate');
    $this->add_xml_if($xml_doc, $xml_obj, 'endDate');

    // contact info fields
    foreach ($this->fields as $f) {
      $xml_obj->appendChild($xml_doc->createElement($f['contactInfoFieldType'], $f['value']));     
    }

    return $xml_obj;
  }

  public function from_xml_fragment($node) {
    if ($node->nodeName != 'contactInfo')
      throw new \Exception('ContactInfo->from_xml_fragment() can only deal with contactInfo nodes');

    $xpath = new \DOMXPath($node->ownerDocument);
    $order = array();

    $fields = $xpath->query('./*', $node);
    foreach ($fields as $f) {
      // Required tags get mapped to member data
      switch ($f->nodeName) {
      case 'contactInfoID':	$this->contactInfoID = $f->nodeValue; break;
      case 'label': 		$this->label = $f->nodeValue; break;
      case 'isPrimary':		$this->isPrimary = $f->nodeValue; break;
      case 'isActive':		$this->isActive = $f->nodeValue; break;
      case 'beginDate':		$this->beginDate = $f->nodeValue; break;
      case 'endDate':		$this->endDate = $f->nodeValue; break;

      case 'siteAcronym':
	$sites = $this->storeFront->SiteStore->getByFilter(array('siteAcronym' => $f->nodeValue));
	$this->siteID = $sites[0]->siteID;
	break;

      default:
	// Try to map other tags to contact info fields
	if (!in_array($f->nodeName, $order)) $order[$f->nodeName] = 0;
	$cifID = $this->storeFront->ContactInfoStore->getFieldTypeIDByName($f->nodeName);

	$this->fields[] = array('contactInfoFieldTypeID' => $cifID,
				'contactInfoFieldType' => $f->nodeName,
				'value' => $f->nodeValue,
				'sortOrder' => ++$order[$f->nodeName]);

	break;
      }
    }

  }

}
