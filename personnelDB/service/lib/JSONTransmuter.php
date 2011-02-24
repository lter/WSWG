<?php

  /**
   *
   *
   * @class JSONTransmuter
   *
   *
   *
   *
   */

require_once('Transmuter.php');


class JSONTransmuter implements Transmuter { 

  private $json_obj = null;
  private $current_element = null;

  /**
   * JSONTransmuter constructor
   *
   */
  public function __construct() { 
	$this->json_obj = array();
  }


  /* OVERLOADED METHODS */

  /**
   * Wrapper for type-specific transmute methods
   *
   * @param mixed $name name of property to serialize
   * @param mixed $value property to serialize
   *
   */
  public function __set($name, $value) { 
	// call appropriate transmuter method
	if (is_array($value)) {
	  $this->transmuteArray($name, $value);
	} elseif (is_object($value)) {
	  $this->transmuteObject($name, $value);
	} else {
	  $this->transmuteScalar($name, $value);
	}
  }

  /**
   * Wrapper for type-specific untransmute methods
   *
   * @param mixed $p_name name of JSON element to unserialize
   *
   */
  public function __get($p_name) { 
	if (isset($this->current_element[$p_name])) {
	  $e = $this->current_element[$p_name];
	  return $this->untransmuteValue($e);
	} else {
	  return null;
	}
  }


  /* TRANSMUTE ENTITY TO JSON */

  /**
   * Transmute scalar PHP types
   *
   * @param string $name name of property to serialize
   * @param mixed $value scalar value to serialize
   *
   */
  public function transmuteScalar($name, $value) { 
	$this->current_element[$name] = $value;
  }


  /**
   * Transmute PHP arrays
   *
   * @param string $name name of property to serialize
   * @param Array array to serialize
   *
   */
  public function transmuteArray($name, $arr) { 
	$t_obj = $this->current_element;
	$this->current_element = array();

	foreach ($arr as $k => $v) { 
	  $this->__set($k, $v);
	}
	$t_obj[$name] = $this->current_element;
	$this->current_element = $t_obj;
  }


  /**
   * Transmute Transmutable objects
   *
   * @param string $name name of property to serialize
   * @param Transmutable $obj object implementing Transmutable interface
   *
   */
  public function transmuteObject($name, $obj) { 
	$t_obj = $this->current_element;
	$this->current_element = array();
	
	$this->transmuteRoot($obj);

	$t_obj[$name] = $this->current_element;
	$this->current_element = $t_obj;
  }


  /**
   * Initialization of object's serialization and call to object's transmuteTo() method
   *
   * @param Transmutable $transmutable object implementing the Transmutable interface
   *
   */
  public function transmuteRoot($transmutable) { 
	if (is_null($this->current_element)) {
	  // If current_element is not set, assume new top-level entity
	  $this->current_element = array();
	  $this->current_element['entity'] = get_class($transmutable);
	  $transmutable->transmuteTo($this);
	  $this->json_obj[] = $this->current_element;
	} else {
	  // Otherwise, assume new entity under existing current_element
	  $this->current_element['entity'] = get_class($transmutable);
	  $transmutable->transmuteTo($this);
	}
  }


  /**
   * Reset append axis to document-level. Call this method
   * after each transmuteRoot() call for writing mutliple entities
   *
   */
  public function reset() { 
	$this->current_element = null;
  }


  /**
   * Return serialization as a string
   *
   */
  public function flush() { 
	return json_encode($this->json_obj);
  }


  /* TRANSMUTE ENTITY FROM JSON */

  /**
   * Type specific method to load entities from a string
   *
   * @param string $entityStr string representation of the entities to be parsed
   *
   */
  public function parse($entityStr) {
	$this->json_obj = json_decode($entityStr, true);
  }
  
  /**
   * Populate a transmutable object from current representation
   *
   * @param Transmutable $transmutable object implementing the Transmutable interface
   *
   */
  public function untransmuteRoot($transmutable) { 
	// Parse data into entity
	$transmutable->transmuteFrom($this);
  }

  /**
   * Recursively convert JSON to a PHP variable
   *
   * @param $e Element to be transmuted
   *
   */
  public function untransmuteValue($e) {
	return $e;
  }

  /**
   * Move to next entity in the loaded JSON document; call this function before each
   *  call to untransmuteRoot to read multiple entities
   *
   */
  public function next() {
	if (is_null($this->current_element)) {
	  $this->current_element = current($this->json_obj);
	} else {
	  $this->current_element = next($this->json_obj);
	}

	if ($this->current_element === false) return false;
	return true;
  }

}