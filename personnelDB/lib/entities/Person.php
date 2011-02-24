<?php

namespace PersonnelDB;

class Person extends Entity {

  /* METHODS */

  public function __construct($inf = null) {
    parent::__construct($inf);
  }

  public function destruct() {
    parent::__destruct();
  }


  /* RELATION METHODS */

  public function getIdentity() { }

  public function getRoles() { }

  public function getContactInfo() { }

}