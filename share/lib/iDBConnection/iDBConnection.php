<?php

namespace WSWG;

// Configuration
define('DBUSER', 'ws_user');
define('DBPASS', 'w$WG.23');
define('DBHOST', 'localhost');
define('DBPORT', '3306');


// iDBConnection class
//  Class to wrap the PHP database functions, providing the following benefits:
//   - abstracted interface to queries, allowing for change of interface/database
//   - shorthand functions for common query tasks
//   - prepare/execute interface without binding variables
//   - singleton database connections, preventing connection bloat
//   - exceptions to replace internal error handling
class iDBConnection {

  /* MEMBER DATA */

  // Internal data structure variables
  private static $instance;			// Self instance, for singleton function

  private $unbuffered = false;			// Result sets are un-buffered
  private $last_unbuffered = false;		// Last result is un-buffered

  protected $mysqli;				// MySQLi resource
  public $last_result;				// Result handler for the most recent query

  protected $prepared = array();		// Prepared statement handlers
  protected $results = array();		        // Results of executed statements

  // Database connection variables
  protected $user;				// Username
  protected $pass;				// Password
  protected $host;				// Hostname
  protected $port;				// Port
  protected $name;				// Database name


  /* METHODS */

  // Private constructor, use getInstance instead
  private function __construct($user, $pass, $host, $port, $name) {
    $this->user = $user;
    $this->pass = $pass;
    $this->host = $host;
    $this->port = $port;
    $this->name = $name;
  }
	
  // Disconnect when this is destroyed
  public function __destruct() {
    // disconnect
    $this->disconnect();
    // free last result if un-buffered
    $this->freeUnbuffered();
  }

  // Returns an instance for the specified database
  //  Use this instead of new
  public function &getInstance($n, $u = DBUSER, $p = DBPASS, $h = DBHOST, $pt = DBPORT) {
    if (!isset(self::$instance[$n])) {
      self::$instance[$n] = new iDBConnection($u, $p, $h, $pt, $n);
      self::$instance[$n]->connect();
    }

    return self::$instance[$n];
  }

  // Connect or throw an exception
  public function connect() {
    $this->mysqli = new mysqli($this->host, $this->user, $this->pass, $this->name, $this->port);

    if ($this->mysqli->connect_error) {
      // Save info and unset to avoid weirdness when disconnecting
      $error = $this->mysqli->connect_error;
      $state = $this->mysqli->sqlstate;
      unset($this->mysqli);

      throw new iDBException($error, 1, $state);
    }
  }
	
  // Close connection or throw an error
  public function disconnect() {
    if (isset($this->mysqli)) {
      // Finish all statements
      foreach (array_keys($this->prepared) as $n) {
	$this->finish($n);
      }
      // Disconnect
      if (!$this->mysqli->close()) {
	throw new iDBException('Could not close connection', 1,
			       $this->mysqli->sqlstate.'('. $this->mysqli->errno.')');
      }
      // Unset mysqli object to avoid future disconnect attempts
      unset($this->mysqli);
    }
  }
	
  // Change database or throw an error
  public function selectDB($name) {
    if (!$this->mysqli->select_db($name)) {
      throw new iDBException($this->mysqli->error, 1,
			     $this->mysqli->sqlstate.'('. $this->mysqli->errno.')');
    }
    $this->name = $name;
  }

  // set character set encoding (read-only)
  public function setEncoding($charset, $collation = null) {
    $this->run("SET CHARACTER SET `$charset`");
  }

  // set character set encoding (read-write)
  public function setNames($charset, $collation = null) {
    $this->run("SET NAMES `$charset`");
  }

  // set whether queries are un-buffered
  public function setUnbuffered($unbuffered=True) { 
    $this->unbuffered = ($unbuffered) ? True : False;
  }

  // free last result if un-buffered
  private function freeUnbuffered() { 
    if (isset($this->last_result) && is_object($this->last_result) && $this->last_unbuffered)  {
      $this->last_result->close();
      unset($this->last_result);
    }
  }

  // Run query or throw an error
  protected function run($sql) {
    // free last result if un-buffered
    $this->freeUnbuffered();

    // execute query
    if (!$this->mysqli->real_query($sql)) {
      throw new iDBException($this->mysqli->error, 2,
			     $this->mysqli->sqlstate.'('. $this->mysqli->errno.')');
    }

    // result sets are either buffered or un-buffered
    if ($this->unbuffered) { 
      $this->last_result = $this->mysqli->use_result();
      $this->last_unbuffered=True;
    } else { 
      $this->last_result = $this->mysqli->store_result();
      $this->last_unbuffered=False;
    }
  }

  // Unset variables related to a statement
  public function finish($n) {
    if (array_key_exists($n, $this->prepared)) {
      // Deallocate statement
      $this->run("DEALLOCATE PREPARE `$n`");
      unset($this->prepared[$n]);
    }

    if (array_key_exists($n, $this->results)) {
      // Deallocate results
      if (is_object($this->results[$n]))
	$this->results[$n]->free();
      unset($this->results[$n]);
    }
  }
	

  /* QUERIES */

  // Run a statement
  public function query($sql) {
    $n = md5($sql);

    $this->run($sql);
    $this->results[$n] = $this->last_result;
    return $n;
  }

  // Run an insert statement and return the inserted ID 	
  public function insert($sql) {
    $this->run($sql);
    return $this->insertId();
  }

  // Run a delete statement and return the number of rows deleted
  public function delete($sql) {
    $this->run($sql);
    return $this->affectedRows();
  }

  // Escape a string using MySQL connection
  public function escape($str) {
    return "'".$this->mysqli->escape_string($str)."'";
  }


  /* PREPARE/EXECUTE */

  // Prepare a statement and return a statement id
  public function prepare($sql) {
    $n = md5($sql);

    if (array_key_exists($n, $this->prepared)) {
      return $n;
    } else {
      $this->run("PREPARE `$n` FROM '$sql'");
      $this->prepared[$n] = $sql;
      return $n;
    }
  }

  // Execute a statement
  public function execute($n, $vars = array()) {
    // Set SQL variables
    foreach ($vars as $i => $v) {
      $slist[] = is_null($v) ? "@{$i} = NULL" : "@{$i} = ".$this->escape($v);
      $vlist[] = "@{$i}";
    }

    // Set variables
    if (!empty($slist)) {
      $this->run("SET ".implode(', ', $slist));
    }

    // Execute statement
    if (empty($vlist)) {
      $this->run("EXECUTE `$n`");
    } else {
      $this->run("EXECUTE `$n` USING ".implode(', ', $vlist));
    }

    $this->results[$n] = $this->last_result;
  }


  /* RETRIEVE DATA */

  public function fetchArray($n = null) {
    if (array_key_exists($n, $this->results)) {
      return $this->results[$n]->fetch_row();
    } else {
      return $this->last_result->fetch_row();
    }
  }

  public function fetchAssoc($n = null) {
    if (array_key_exists($n, $this->results)) {
      return $this->results[$n]->fetch_assoc();
    } else {
      return $this->last_result->fetch_assoc();
    }
  }

  public function fetchObject($n = null) {
    if (array_key_exists($n, $this->results)) {
      return $this->results[$n]->fetch_object();
    } else {
      return $this->last_result->fetch_object();
    }
  }

  public function numRows($n = null) {
    if (array_key_exists($n, $this->results)) {
      return $this->results[$n]->num_rows;
    } else {
      return $this->last_result->num_rows;
    }
  }

  public function insertId() {
    return $this->mysqli->insert_id;
  }

  public function affectedRows() {
    return $this->mysqli->affected_rows;
  }


  /* TRANSACTIONS */

  // Begin a transaction
  public function startTransaction() {
    $this->mysqli->autocommit(false);
  }

  // Commit a transaction
  public function endTransaction() {
    $this->mysqli->commit();
  }

  // Undo a transaction
  public function rollback() {
    $this->mysqli->rollback();
  }


  /* SPECIAL RETURNS */
  /* 
     Be careful using getCol and getAll with very large queries, these
     methods can make PHP run out of memory!
  */

  // Return the first value of the the first row
  public function getOne($sql) {
    $this->run($sql);
    $a = $this->fetchArray();
    return $a[0];
  }

  // Return the first row as a stdObject
  public function getRow($sql) {
    $this->run($sql);
    $o = $this->fetchObject();
    return $o;
  }

  // Return the first value for all rows	
  public function getCol($sql) {
    $this->run($sql);
    $a = array();

    while($row = $this->fetchArray()) {
      $a[] = $row[0];
    }

    return $a;
  }
	
  // Get all rows as stdObjects
  public function getAll($sql) {
    $this->run($sql);
    $a = array();

    while($row = $this->fetchObject()) {
      $a[] = $row;
    }

    return $a;
  }
}


// iDBException class
//  Custom exception type for iDBConnection
class iDBException extends \Exception {
  
  private $dbdebug;
  private $decode = array(0 => 'Unknown',
			  1 => 'Connection error',
			  2 => 'Query error');
  
  public function __construct($message, $code = 0, $dbdebug = '') {
    parent::__construct($message, $code);
    $this->dbdebug = $dbdebug;
  }
  
  public function __toString() {
    return "'{$this->decode[$this->code]} ({$this->code}): {$this->message}' on line {$this->line} in {$this->file}\nDatabase error: {$this->dbdebug}";
    }

  final public function getDBDebug() {
    return $this->dbdebug;
  }
}