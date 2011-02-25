<?php

namespace PersonnelDB;

require_once('Store.php');
require_once('personnelDB/SQL/ContactInfoSQL.php');
require_once('personnelDB/entities/ContactInfo.php');

class ContactInfoStore extends Store {

  /* METHODS */
  
  public function __construct() {
    parent::__construct();

    $this->filterList = array (
			       'isActive' => array('isActive'),
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

  // Returns an empty ContactInfo entity
  public function getEmpty() {
    return new ContactInfo();
  }

  // Returns all contact info blocks in the database
  public function getAll() {
    return $this->makeEntityArray('ContactInfo', CONTACT_GETALL);
  }

  // Returns a single contact info block matching $id, or null if no match exists
  public function getById($id) {
    $list = $this->makeEntityArray('ContactInfo', CONTACT_GETBYID, array($id));
    return isset($list[0]) ? $list[0] : null;
  }

  // Returns an array of contact info blocks matching the filter/value pairs
  //  given in $filters, or null if there are no matches
  public function getByFilter($filters = array()) {
    return $this->makeFilteredArray('ContactInfo', CONTACT_GETBYFILTER_STUB, $filters);
  }

  // Gets an array of fields associated with contact info block $id
  public function getFields($id) {
    $f = function($e) { return array(null, $e); };
    return $this->makeArray($f, FIELD_GETBYCONTACT, array($id));
  }

  // Gets a field type id given the field type name
  public function getFieldTypeIDByName($name) {
    $f = function($e) { return array($e['contactInfoFieldType'], $e['contactInfoFieldTypeID']); };
    $list = $this->makeArray($f, FIELDTYPE_GETBYNAME, array($name));
    return $list[$name];
  }

  /* UPDATE METHODS */

  public function put() { }

  private function putContactInfoFields() { }

}