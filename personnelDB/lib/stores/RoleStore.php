<?php

namespace PersonnelDB;

class RoleStore extends Store {

  /* METHODS */
  
  public function __construct() {
    parent::__construct();
  }
  
  public function __destruct() {
    parent::__destruct();
  }


  /* ACCESS METHODS */

  // Returns a new role
  public function getEmpty() { 
    return new Role();
  }

  // Returns an array of all the roles in the database
  public function getAll() {
    return $this->makeEntityArray('Role', ROLE_GETALL);
  }

  // Returns a role given it's id
  public function getById($id) { 
    $list = $this->makeEntityArray('Role', ROLE_GETBYID, array($id));
    return isset($list[0]) ? $list[0] : null;
  }

  /* UPDATE METHODS */

    public function put() {
    }

}
