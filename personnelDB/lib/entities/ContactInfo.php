<?php

namespace PersonnelDB;

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
  public function to_xml() {
    $this->xml_obj = new DOMElement('ContactInfo');
    $this->xml_obj->appendChild(new DOMElement('contactInfoID'),$this->contactInfoID );
    $this->xml_obj->appendChild(new DOMElement('lable'),$this->lable );
    $this->xml_obj->appendChild(new DOMElement('isPrimary'),$this->isPrimary );
    $this->xml_obj->appendChild(new DOMElement('isActive'),$this->isActive );
    // address
    $this->xml_obj->appendChild(new DOMElement('instituation'),$this->instituation );
    $this->xml_obj->appendChild(new DOMElement('city'),$this->city );
    $this->xml_obj->appendChild(new DOMElement('administrativeArea'),$this->administrativeArea );
    $this->xml_obj->appendChild(new DOMElement('postalCode'),$this->postalCode );
    $this->xml_obj->appendChild(new DOMElement('country'),$this->country );
    // phone
    // fax
    // email

    return $this->xml_obj;
  }

  public function from_xml() {

  }
}
