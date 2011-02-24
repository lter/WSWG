<?php

namespace PersonnelDB;

//require_once('Transmuter/Transmutable.php');

abstract class Entity {

  /* MEMBER DATA */

  protected $storeFront;
  private $contents;
  public $uniqueId;


  /* METHODS */

  public function __construct($inf) {
    // Take associative array as member data
    if (is_array($inf)) {
      $this->contents = $inf;
    } else {
      $this->contents = array();
    }

    // Local DataFormat copy for relational methods
    $this->storeFront = PersonnelDB::getInstance();
  }

  public function __destruct() {

  }

  /* OVERLOADED METHODS */

  // Getting an unavailable member variable will check the contents array, then
  //   the metadata array (if it exists)
  public function __get($field) {
    if (isset($this->contents[$field])) {
      return $this->contents[$field];
    } else {
      return null;
    }
  }

  public function __set($field, $value) {
    $this->contents[$field] = $value;
  }
  
}