<?php

namespace PersonnelDB;

require_once('Store.php');
require_once('personnelDB/SQL/RoleTypeSQL.php');
require_once('personnelDB/entities/RoleType.php');

class RoleTypeStore extends Store {

  /* METHODS */
  
  public function __construct() {
    parent::__construct();

    $this->filterList = array (
			       'name' => array('roleType'),
			       'scope' => array('site', 'siteAcronym'),
			       );
  }
  
  public function __destruct() {
    parent::__destruct();
  }


  /* ACCESS METHODS */

  // Returns a new roleType entity
  public function getEmpty() { 
    return new RoleType();
  }

  // Returns an array of all the roleTypes in the database
  public function getAll() {
    return $this->makeEntityArray('RoleType', ROLETYPE_GETALL);
  }

  // Returns an array of roleTypes of the given $type (nsf or local)
  public function getByType($type) {
    switch ($type) {
    case 'nsf': $sql = ROLETYPE_GETALL_NSF; break;
    case 'local': $sql = ROLETYPE_GETALL_LOCAL; break;
    default: throw new \Exception("Type must be 'nsf' or 'local'");
    }

    return $this->makeEntityArray('RoleType', $sql);
  }

  // Returns a single roleType of $type matching $id, or null if no match exists
  public function getById($id, $type) {
    switch ($type) {
    case 'nsf': $sql = ROLETYPE_GETBYID_NSF; break;
    case 'local': $sql = ROLETYPE_GETBYID_LOCAL; break;
    default: throw new \Exception("Type must be 'nsf' or 'local'"); break;
    }

    $list = $this->makeEntityArray('RoleType', $sql, array($id));
    return isset($list[0]) ? $list[0] : null;
  }

  // Returns an array of roleTypes matching the filter/value pairs
  //  given in $filters, or null if there are no matches; optional
  //  $type parameter restricts to nsf or local roleTypes
  public function getByFilter($filters = array(), $type = null) {
    switch ($type) {
    case 'nsf': $sql = ROLETYPE_GETBYFILTER_NSF_STUB; break;
    case 'local': $sql = ROLETYPE_GETBYFILTER_LOCAL_STUB; break;
    default: $sql = ROLETYPE_GETBYFILTER_STUB; break;
    }

    return $this->makeFilteredArray('RoleType', $sql, $filters);
  }


  /* UPDATE METHODS */

  public function put() {
  }

}
