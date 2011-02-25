<?php

namespace PersonnelDB;
use \DOMElement as DOMElement;

class ContactInfo extends Entity {

  /* METHODS */

  public function __construct($inf = null) {
    parent::__construct($inf);

    // Populate contact info fields
    $this->contactInfoFields = $this->storefront->ContactInfoStore->getFields($this->contactInfoID);
  }

  public function destruct() {
    parent::__destruct();
  }


  /* RELATION METHODS */

  public function getPerson() { }

  public function getSite() { }

  /* serialization */
  // returns a representation of itself as an xml fragment that conforms to the personelDB.xsd 
  public function to_xml() {
    $xml_obj = new DOMElement('ContactInfo');
    $xml_obj->appendChild(new DOMElement('contactInfoID',$this->contactInfoID ));
    $xml_obj->appendChild(new DOMElement('label',$this->label ));
    $xml_obj->appendChild(new DOMElement('isPrimary',$this->isPrimary ));
    $xml_obj->appendChild(new DOMElement('isActive',$this->isActive ));
    
    // address
    if ($this->contactInfoFields.length > 0 ){
      $xml_obj->appendChild(new DOMElement('address',$this->address ));
    }
    $xml_obj->appendChild(new DOMElement('instituation',$this->instituation ));
    $xml_obj->appendChild(new DOMElement('city',$this->city ));
    $xml_obj->appendChild(new DOMElement('administrativeArea',$this->administrativeArea ));
    $xml_obj->appendChild(new DOMElement('postalCode',$this->postalCode ));
    $xml_obj->appendChild(new DOMElement('country',$this->country ));
    // phone 
    if ($this->contactInfoFields.length > 0 )  {
      $xml_obj->appendChild(new DOMElement('phone',$this->phone ));
    }

    // fax
    if ($this->contactInfoFields.length > 0) {
      $xml_obj->appendChild(new DOMElement('fax',$this->fax ));
    }

    // email
    if ($this->contactInfoFields.length > 0 ) {
      $xml_obj->appendChild(new DOMElement('email',$this->email ));
    }
    return $xml_obj;
  }

  public function from_xml($xml_dom) {
    if ($xml_dom->nodeName == 'contactInfo')
      throw new \Exception('contactInfo->from_xml can only deal with contactInfo nodes');
     }
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
    foreach($xpath.query("*/fax") as $i => $fax {
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
      // $field->contactInfoFieldTypeID = $this->storefront->contactInfoFieldTypeStore->
      return $field;
  }
}
