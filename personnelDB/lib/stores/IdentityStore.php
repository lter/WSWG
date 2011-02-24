<?php

namespace PersonnelDB;

// Include abstract parent class
require_once('Store.php');


class IdentityStore extends Store {

  /* MEMBER DATA */



  /* METHODS */
  
  public function __construct() {
    parent::__construct();

    $this->filterList = array (
			       'name' => array('firstName', 'middleName', 'lastName', 'preferredName', 'nameAlias'),
			       );
  }
  
  public function __destruct() {
    parent::__destruct();
  }


  /* ACCESS METHODS */

  // Returns an empty Identity entity
  public function getEmpty() {
    return new Identity();
  }

  // Returns an array of all Identity entities in the database
  public function getAll() {
    return $this->makeEntityArray('Identity', IDENTITY_GETALL);
  }

  // Returns a single Identity entity matching $id, or null if no match exists
  public function getById($id) {
    $list = $this->makeEntityArray('Identity', IDENTITY_GETBYID, array($id));
    return isset($list[0]) ? $list[0] : null;
  }

  // Returns an array of Identity objects matching the filter/value pairs
  //  given in $filters, or null if there are no matches
  public function getByFilter($filters = array()) {
    return $this->makeFilteredArray('Identity', IDENTITY_GETBYFILTER_STUB, $filters);
  }

  public function getAliases() {
    $f = function($e) { return array(null, $e['nameAlias']); };
    return $this->makeArray($f, IDENTITY_GETALIAS, array($id));
  }


  /* UPDATE METHODS */

  public function put() { }

  private function putAliases() { }

}