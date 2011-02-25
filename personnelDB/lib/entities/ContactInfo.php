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

  public function from_xml($xml_string) {

  }
}
