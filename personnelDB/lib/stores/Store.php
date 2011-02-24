<?php

namespace PersonnelDB;

require_once('iDBConnection/iDBConnection.php');

// Configuration
define('DATABASE', 'lter_personnel');


abstract class Store {

  /* MEMBER DATA */

  protected $iDBConnection;


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
    $sth = $this->iDBConnection->prepare($stmt);
    $this->iDBConnection->execute($sth, $vars);

    $ret = array();
    while ($e = $this->iDBConnection->fetchAssoc($sth)) {
      $ret[] = new $class($e);
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

}