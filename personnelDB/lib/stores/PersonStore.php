<?php

namespace PersonnelDB;

require_once('Store.php');
require_once('personnelDB/SQL/PersonSQL.php');
require_once('personnelDB/entities/Person.php');

class PersonStore extends Store {

  /* METHODS */
  
  public function __construct() {
    parent::__construct();

    $this->filterList = array (
			       'name' => array('firstName', 'middleName', 'lastName', 'preferredName', 'nameAlias'),
			       'lastName' => array('lastName'),
			       'isActive' => array('nsfRole.isActive', 'localRole.isActive'),
			       'roleType' => array('nsfRoleType.roleName', 'localRoleType.roleName'),
			       'site' => array('s1.site', 's1.siteAcronym', 's2.site', 's2.siteAcronym'),
			       'siteAcronym' => array('s1.siteAcronym', 's2.siteAcronym'),
			       );
  }
  
  public function __destruct() {
    parent::__destruct();
  }


  /* ACCESS METHODS */

  // Returns an empty Person entity
  public function getEmpty() {
    return new Person();
  }

  // Returns an array of all people in the database
  public function getAll() {
    return $this->makeEntityArray('Person', PERSON_GETALL);
  }

  // Returns a single person matching $id, or null if no match exists
  public function getById($id) {
    $list = $this->makeEntityArray('Person', PERSON_GETBYID, array($id));
    return isset($list[0]) ? $list[0] : null;
  }

  // Returns an array of people matching the filter/value pairs
  //  given in $filters, or null if there are no matches
  public function getByFilter($filters = array()) {
    return $this->makeFilteredArray('Person', PERSON_GETBYFILTER_STUB, $filters);
  }


  /* UPDATE METHODS */

  public function insert($person) {
    $storeFront = PersonnelDB::getInstance();

    // Insert new Identity information
    $person->identity = $storeFront->IdentityStore->insert($person->identity);
    $person->personID = $person->identity->personID;

    // Insert roles and contacts
    foreach ($person->roles as $role) {
      $role->personID = $person->personID;
      $storeFront->RoleStore->insert($role);
    }

    foreach ($person->contacts as $contact) {
      $contact->personID = $person->personID;
      $storeFront->ContactInfoStore->insert($contact);
    }

    return $this->getById($person->personID);
  }

  public function update($person) {

  }

}