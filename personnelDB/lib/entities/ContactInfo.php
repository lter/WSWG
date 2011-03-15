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
    $this->add_xml_if($xml_doc, $xml_obj, 'contactInfoID');
    $this->add_xml_if($xml_doc, $xml_obj, 'label');
    $this->add_xml_if($xml_doc, $xml_obj, 'isPrimary');
    $this->add_xml_if($xml_doc, $xml_obj, 'isActive');

    // contact info fields
    foreach ($this->fields as $f) {
      $xml_obj->appendChild($xml_doc->createElement($f['contactInfoFieldType'], $f['value']));     
    }

    return $xml_obj;
  }

  public function from_xml($xml_dom) {
    if ($xml_dom->nodeName == 'contactInfo')
      throw new \Exception('contactInfo->from_xml can only deal with contactInfo nodes');

    $xpath = new \DOMXPath($xml_dom);
    $this->contactInfoID = $xpath.query("*/contactInfoID/")->nodeValue;
    $this->label = $xpath.query("*/label/")->nodeValue;
    $this->isPrimary = $xpath.query("*/isPrimary/")->nodeValue;
    $this->isActive = $xpath.query("*/isActive/")->nodeValue;
    // address
    foreach($xpath.query("*/address") as $i => $address) {
      $this->contactInfoFields[] = assemble_contactInfoFields($phone, 'address');
    }
    unset($address);
    $this->institution = $xpath.query("*/institution/")->nodeValue;
    $this->city = $xpath.query("*/city/")->nodeValue;
    $this->administrativeArea = $xpath.query("*/administrativeArea/")->nodeValue;
    $this->postalCode = $xpath.query("*/postalCode/")->nodeValue;
    $this->county = $xpath.query("*/county/")->nodeValue;
    //phone
    foreach($xpath.query("*/phone") as $i => $phone) {
      $this->contactInfoFields[] = assemble_contactInfoFields($phone, 'phone');
    }
    unset($phone);
    //fax
    foreach($xpath.query("*/fax") as $i => $fax) {
      $this->contactInfoFields[] = assemble_contactInfoFields($phone, 'fax');
    }
    unset($fax);
    //email
    foreach($xpath.query("*/email") as $i => $email) {
      $this->contactInfoFields[] = assemble_contactInfoFields($email, 'email');
    }
    unset($email);
  }

  private function assemble_contactInfoFields($field, $type_string){
    $field= new ContactInfoField();
    $field->value = $phone->nodeValue;
    $field->contactInfoID = $this.contactInfoID;
    $field->sortOrder = $i;
    // TODO: grab the correct ID
    $field->contactInfoFieldTypeID = $this->getContactInfoFieldTypeBy($type_string);
    return $field;
  }

}
