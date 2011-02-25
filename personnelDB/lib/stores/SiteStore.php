<?php

namespace PersonnelDB;

require_once('Store.php');
require_once('personnelDB/SQL/SiteSQL.php');
require_once('personnelDB/entities/Site.php');

class SiteStore extends Store {

  /* METHODS */
  
  public function __construct() {
    parent::__construct();

    $this->filterList = array (
			       'site' => array('site', 'siteAcronym'),
			       'siteAcronym' => array('siteAcronym'),
			       );
  }
  
  public function __destruct() {
    parent::__destruct();
  }


  /* ACCESS METHODS */

  // Returns a new site entity
  public function getEmpty() { 
    return new Site();
  }

  // Returns an array of all the sites in the database
  public function getAll() {
    return $this->makeEntityArray('Site', SITE_GETALL);
  }

  // Returns a single site matching $id, or null if no match exists
  public function getById($id) {
    $list = $this->makeEntityArray('Site', SITE_GETBYID, array($id));
    return isset($list[0]) ? $list[0] : null;
  }

  // Returns an array of sites matching the filter/value pairs
  //  given in $filters, or null if there are no matches
  public function getByFilter($filters = array()) {
    return $this->makeFilteredArray('Site', SITE_GETBYFILTER_STUB, $filters);
  }


  /* UPDATE METHODS */

  public function put() { }

}