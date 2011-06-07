<?php

namespace PersonnelDB;
use \Exception as Exception;

require_once('Store.php');
require_once('personnelDB/SQL/RoleSQL.php');
require_once('personnelDB/entities/Role.php');

class RoleStore extends Store {

  /* METHODS */
  
  public function __construct() {
    parent::__construct();

    $this->filterList = array (
			       'isActive' => array('isActive'),
			       'roleType' => 'roleName',
			       'site' => array('site', 'siteAcronym'),
			       'siteAcronym' => array('siteAcronym'),
			       'personID' => array('personID'),
			       'name' => array('firstName', 'middleName', 'lastName', 'preferredName', 'nameAlias'),
			       'lastName' => array('lastName'),
			       );
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
    $n = $this->getByType('nsf');
    $l = $this->getByType('local');
    return array_merge($n, $l);
  }

  // Returns an array of roles of the given $type (nsf or local)
  public function getByType($type) {
    switch ($type) {
    case 'nsf': $sql = ROLE_GETALL_NSF; break;
    case 'local': $sql = ROLE_GETALL_LOCAL; break;
    default: throw new Exception("Type must be 'nsf' or 'local'");
    }

    return $this->makeEntityArray('Role', $sql);
  }

  // Returns a single role of $type matching $id, or null if no match exists
  public function getById($id, $type) {
    switch ($type) {
    case 'nsf': $sql = ROLE_GETBYID_NSF; break;
    case 'local': $sql = ROLE_GETBYID_LOCAL; break;
    default: throw new Exception("Type must be 'nsf' or 'local'");
    }

    $list = $this->makeEntityArray('Role', $sql, array($id));
    return isset($list[0]) ? $list[0] : null;
  }

  // Returns an array of Role objects matching the filter/value pairs
  //  given in $filters, or null if there are no matches
  public function getByFilter($filters = array(), $type = null) {
    switch ($type) {
    case 'nsf':  return $this->makeFilteredArray('Role', ROLE_GETBYFILTER_NSF_STUB, $filters);
    case 'local': return $this->makeFilteredArray('Role', ROLE_GETBYFILTER_LOCAL_STUB, $filters);
    default:
      $n = $this->getByFilter($filters, 'nsf');
      $l = $this->getByFilter($filters, 'local');
      return array_merge($n, $l);
    }

    return $this->makeFilteredArray('Role', $sql, $filters);
  }

  /* UPDATE METHODS */

  public function put() {
  }

}
