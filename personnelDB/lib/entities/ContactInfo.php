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

}