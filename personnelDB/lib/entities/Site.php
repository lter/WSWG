<?php

namespace PersonnelDB;

class Site extends Entity {

  /* METHODS */

  public function __construct($inf = null) {
    parent::__construct($inf);
  }

  public function destruct() {
    parent::__destruct();
  }


  /* RELATION METHODS */

  public function getRoles() { }

  public function getContactInfo() { }

}