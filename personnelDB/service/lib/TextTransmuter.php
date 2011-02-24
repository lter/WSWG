<?php

  /**
   *
   *
   * @class TextTransmuter
   *
   *
   *
   *
   */

require_once('Transmuter.php');


class TextTransmuter implements Transmuter { 

  private $text_str = null;
  private $current_element = null;
  private $current_depth = 0;


  /**
   * TextTransmuter constructor
   *
   */
  public function __construct() { 
	$this->text_str = '';
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
   * @param mixed $p_name name of property to unserialize
   *
   */
  public function __get($p_name) { 

  }


  /* TRANSMUTE ENTITY TO TEXT */

  /**
   * Transmute scalar PHP types
   *
   * @param string $name name of property to serialize
   * @param mixed $value scalar value to serialize
   *
   */
  public function transmuteScalar($name, $value) { 
	$this->current_element .= str_repeat("\t", $this->current_depth)."$name: $value\n";
  }


  /**
   * Transmute PHP arrays
   *
   * @param string $name name of property to serialize
   * @param Array array to serialize
   *
   */
  public function transmuteArray($name, $arr) { 
	$t_str = $this->current_element.str_repeat("\t", $this->current_depth)."$name:\n";
	$this->current_element = '';
	$this->current_depth += 1;

	foreach ($arr as $k => $v) { 
	  if (is_numeric($k)) $k = "[ $k ]";
	  $this->__set($k, $v);
	}

	$this->current_depth -= 1;
	$this->current_element = $t_str.$this->current_element;
  }


  /**
   * Transmute Transmutable objects
   *
   * @param string $name name of property to serialize
   * @param Transmutable $obj object implementing Transmutable interface
   *
   */
  public function transmuteObject($name, $obj) { 
	$t_str = $this->current_element.str_repeat("\t", $this->current_depth)."$name:\n";
	$this->current_element = '';
	$this->current_depth += 1;

	$this->transmuteRoot($obj);

	$this->current_depth -= 1;
	$this->current_element = $t_str.$this->current_element;
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
	  $this->current_element = "Entity: ".get_class($transmutable)."\n";
	  $this->current_depth = 1;
	  $transmutable->transmuteTo($this);
	  $this->text_str .= $this->current_element."\n";
	} else {
	  // Otherwise, assume new entity under existing current_element
	  $this->current_element = str_repeat("\t", $this->current_depth)."Entity: ".get_class($transmutable)."\n";
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
   * Return text string representation
   *
   */
  public function flush() { 
	return $this->text_str;
  }

}