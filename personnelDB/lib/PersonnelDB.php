<?php

namespace PersonnelDB;

ini_set('include_path', '.:/Users/mkortz/Source/WSWG/share/lib');

// Include stores
include('stores/IdentityStore.php');

// Include entities
include('entities/Identity.php');

// Include SQL
include('SQL/IdentitySQL.php');


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