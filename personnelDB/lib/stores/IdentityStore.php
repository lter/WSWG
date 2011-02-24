<?php

namespace PersonnelDB;

class IdentityStore extends Store {

  /* METHODS */
  
  public function __construct() {
    parent::__construct();
  }
  
  public function __destruct() {
    parent::__destruct();
  }


  /* ACCESS METHODS */

  public function getEmpty() { }

  public function getAll() {
    return $this->makeEntityArray('Identity', IDENTITY_GETALL);
  }

  public function getById($id) {
    $list = $this->makeEntityArray('Identity', IDENTITY_GETBYID, array($id));
    return isset($list[0]) ? $list[0] : null;
  }

  public function getByFilter($filter = array()) {
    $sql = IDENTITY_GETBYFILTER_STUB;
  }

  public function getAliases() {
    $f = function($e) { return array(null, $e['nameAlias']); };
    return $this->makeArray($f, IDENTITY_GETALIAS, array($id));
  }


  /* UPDATE METHODS */

  public function put() { }

  private function putAliases() { }

}