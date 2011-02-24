<?php

namespace PersonnelDB;

class PersonStore extends Store {

  /* METHODS */
  
  public function __construct() {
    parent::__construct();
  }
  
  public function __destruct() {
    parent::__destruct();
  }


  /* ACCESS METHODS */

  public function getEmpty() {
    return new Person();
  }

  public function getAll() {
  }

  public function getById($id) {

  }

  public function getByFilter() { }


  /* UPDATE METHODS */

  public function put() { }

}