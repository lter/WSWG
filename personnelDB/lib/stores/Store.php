<?php

namespace PersonnelDB;
use \Exception as Exception;

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
    foreach ($filters as $filter => $values) {
      if (array_key_exists($filter, $this->filterList)) {
	// Create array for this filter if needed
	if (!array_key_exists($filter, $where))
	  $where[$filter] = array();
	
	// Create separate constraints for each value
	$values = is_array($values) ? $values : array($values);
	foreach ($values as $value) {
	  $not = (substr($value,0,1)) == '!' ? '!' : '';
	  $value = ltrim($value, '!');
	  $fields = $this->filterList[$filter];

	  // For each DB field mapped to this filter, add a field to the constraint
	  $where[$filter][] = array('not' => $not, 'value' => $value, 'fields' => $fields);
	}
      } else {
	throw new Exception("'$filter' is not a valid filter");
      }
    }

    // Compile WHERE clause and append to query stub
    $wherePieces = array();
    $queryVars = array();
    foreach ($where as $filter => $constraints) {
      $filterPieces = array();
      foreach ($constraints as $constraint) {
	$constraintPieces = array();
	foreach ($constraint['fields'] as $field) {
	  if (is_numeric($constraint['value'])) {
	    $constraintPieces[] = "{$field} = ?";
	    $queryVars[] = $constraint['value'];
	  } else {
	    $constraintPieces[] = "{$field} LIKE ?";
	    $queryVars[] = "%{$constraint['value']}%";
	  }
	}

	// Within a constraint, fields are ORed
	$filterPieces[] = "{$constraint['not']}(".implode(' OR ', $constraintPieces).")";
      }
	
      // Within a filter, constraints are ORed
      $wherePieces[] = '('.implode(' OR ', $filterPieces).')';
    }

    // Between filters, constraints are ANDed
    $sql = empty($wherePieces) ? $stub : "$stub WHERE ".implode(' AND ', $wherePieces);

    // Execute query and return an entity array
    return $this->makeEntityArray($class, $sql, $queryVars);
  }
}