<?php

namespace PersonnelDB;

class PersonnelDB {

  private static $storeFront;
  private $stores = array();

  /* METHODS */

  private function __construct() {
    // Singleton class, use getInstance
  }

  public function __destruct() {

  }

  // Returns an instance of the PersonnelDB class
  // Use this instead of new
  public static function getInstance() {
    if (!isset(self::$storeFront)) {
      self::$storeFront = new PersonnelDB();
    }
	
    return self::$storeFront;
  }

  // Input: $a, $b: Arrays to be intersected; array members must all be one class
  // Return: An array containing the intersection between $a and $b
  public function intersection($a, $b) {
	return array_uintersect($a, $b, 'PersonnelDB::compare');
  }

  // Input: $a, $b: Arrays to be union; array members must all be one class
  // Return: An array containing the union of $a and $b
  public function union($a, $b) {
	return array_merge($a, array_udiff($b, $a, 'PersonnelDB::compare'));
  }

  // Input: $a, $b: Enities to be compared
  // Return: -1 if a < b, 1 if a > b, 0 if a==b
  private static function compare($a, $b) {
	if (get_class($a) != get_class($b)) {
	  // Entities should not be compared if they are not the same type
	  throw new Exception('Attempt to compare '.get_class($a).' to '.get_class($b));
	} elseif ($a->uniqueId > $b->uniqueId) {
	  return 1;
	} elseif ($a->uniqueId < $b->uniqueId) {
	  return -1;
	} else {
	  return 0;
	}
  }

  /* OVERLOADED METHODS */

  // Overload for getting member data
  // Access stores as member data by name
  public function __get($store) {
    if (isset($this->stores[$store])) {
      // If the store has been instantiated, return it
      return $this->stores[$store];
    } elseif (class_exists($store)) {
      // Otherwise, instantiate and return
      $this->stores[$store] = new $store();
      return $this->stores[$store];
    } else {
      // This is not a recognized store class!
      throw new Exception('Attempt to create unknown store type: '.$store);
    }
  }
}