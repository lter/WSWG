<?php

namespace PersonnelDB;

require_once('iDBConnection/iDBConnection.php');

// Configuration
define('DATABASE', 'lter_personnel');


abstract class Store {

  /* MEMBER DATA */

  // Database connection
  protected $iDBConnection;

  // List of available filters for this entity, populated in constructor
  protected $filterList = array();


  /* METHODS */

  // Constructor
  public function __construct() {
    // Get a connection to the database
    $this->iDBConnection =& \WSWG\iDBConnection::getInstance(DATABASE);
    $this->iDBConnection->setEncoding('utf8');
  }

  // Destructor
  public function __destruct() {

  }
 
  // Input: $class: class name used to instantiate objects
  //	    $stmt: SQL statement to be run
  //	    $vars: Variables to bind to $stmt
  // Return: An array of objects created from values returned by $stmt 
  protected function makeEntityArray($class, $stmt, $vars = array()) {
    $fqClass = __NAMESPACE__."\\".$class;
    $sth = $this->iDBConnection->prepare($stmt);
    $this->iDBConnection->execute($sth, $vars);

    $ret = array();
    while ($e = $this->iDBConnection->fetchAssoc($sth)) {
      $ret[] = new $fqClass($e);
    }
	  
    $this->iDBConnection->finish($sth);
    return $ret;
  }

  // Input: $callback: callback used to construct array elements, which should take
  //	      statement result $e as its only parameter and return an array of 
  //          one key and one value to be added to the return array
  //	    $stmt: SQL statement to be run
  //	    $vars: Variables to bind to $stmt
  // Return: An array of data structures created from values returned by $stmt 
  protected function makeArray($callback, $stmt, $vars = array()) {
    $sth = $this->iDBConnection->prepare($stmt);
    $this->iDBConnection->execute($sth, $vars);

    $ret = array();
    while ($e = $this->iDBConnection->fetchAssoc($sth)) {
      list($key, $value) = call_user_func($callback, $e);
      if (is_null($key)) $ret[] = $value;
      else $ret[$key] = $value;
    }
	  
    $this->iDBConnection->finish($sth);
    return $ret;
  }

  // Input: $class: class name used to instantiate objects
  //	    $stub: SQL statement that will be modified and run
  //	    $filters: associative array of filters to values or value arrays
  // Return: An array of objects created by applying filter constraints to $stub
  public function makeFilteredArray($class, $stub, $filters) {
    $where = array();

    // Create a nested array of constraints from filter/value pairs, throw
    //  an exception if an unknown filter is found
    foreach ($filters as $filter => $value) {
      if (array_key_exists($filter, $this->filterList)) {
	// Create array for this filter if needed
	if (!array_key_exists($filter, $where))
	  $where[$filter] = array();
	
	// For each DB field mapped to this filter, add a constraint
	foreach ($this->filterList[$filter] as $field) {
	  // If an array of values was passed, create separate constraints for each value
	  if (is_array($value))
	    foreach ($value as $v) { $where[$filter][] = array($field, $v); }
	  else
	    $where[$filter][] = array($field, $value);
	}
      } else {
	throw new \Exception("'$filter' is not a valid Identity filter");
      }
    }

    // Compile WHERE clause and append to query stub
    $wherePieces = array();
    $queryVars = array();
    foreach ($where as $filter => $constraints) {
      $subWherePieces = array();
      foreach ($constraints as $constraint) {
	$subWherePieces[] = "{$constraint[0]} LIKE ?";
	$queryVars[] = "%{$constraint[1]}%";
      }

      // Within a filter, constraints are ORed
      $wherePieces[] = '('.implode(' OR ', $subWherePieces).')';
    }

    // Between filters, constraints are ANDed
    $sql = empty($wherePieces) ? $stub : "$stub WHERE ".implode(' AND ', $wherePieces);

    // Execute query and return an entity array
    return $this->makeEntityArray($class, $sql, $queryVars);
  }
}