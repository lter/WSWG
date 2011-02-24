<?php

/**
 *
 *
 * @class XMLTransmuter
 *
 *
 *
 *
 */

require_once('Transmuter.php');


class XMLTransmuter implements Transmuter { 

  private $xml_obj;
  private $xml_root;

  private $current_node = null;
  private $current_index = null;

  public function __construct() { 
	$this->xml_obj = new DOMDocument('1.0', 'utf-8');
	$this->xml_root = $this->xml_obj->appendChild($this->xml_obj->createElement('entities'));
  }


  /* OVERLOADED METHODS */

  /**
   * Wrapper for type-specific transmute methods
   *
   * @param mixed $p_name name or index (for an array element) of object attribute to serialize
   * @param mixed $p_value value to be set
   *
   */
  public function __set($p_name, $p_value) { 

	// accomodate numeric keys for XML
	if (is_numeric($p_name)) {
	  $e = $this->current_node->appendChild($this->xml_obj->createElement('element'));
	  $e->setAttribute('index', $p_name);
	} else {
	  $e = $this->current_node->appendChild($this->xml_obj->createElement($p_name));
	}

	// call appropriate transmuter method
	if (is_array($p_value)) {
	  $this->transmuteArray($p_value, $e);
	} elseif (is_object($p_value)) {
	  $this->transmuteObject($p_value, $e);
	} else {
	  $this->transmuteScalar($p_value, $e);
	}

  }

  /**
   * Wrapper for type-specific untransmute methods
   *
   * @param mixed $p_name name of XML element to unserialize
   *
   */
  public function __get($p_name) { 
	$e = $this->current_node->getElementsByTagName($p_name)->item(0);
	return $this->untransmuteValue($e);
  }



  /* TRANSMUTE ENTITY TO XML */

  /**
   * Transmute scalar PHP types
   *
   * @param mixed $p_value scalar (non-array, non-object) value to serialize
   * @param DOMElement $e XML DOM parent element
   */
  public function transmuteScalar($p_value, $e) { 
	return $e->appendChild($this->xml_obj->createTextNode($p_value));
  }


  /**
   * Transmute PHP arrays
   *
   * @param Array $p_value PHP array to be serialized
   * @param DOMElement $e XML DOM parent element
   *
   */
  public function transmuteArray($p_value, $e) { 
	$t_node = $this->current_node;
	$this->current_node = $e;

	// transmute each key value pair individually
	foreach ($p_value as $k => $v) {
	  $e = $this->current_node->appendChild($this->xml_obj->createElement('element'));
	  $e->setAttribute('index', $k);

	  // call appropriate transmuter method
	  if (is_array($v)) {
		$this->transmuteArray($v, $e);
	  } elseif (is_object($v)) {
		$this->transmuteObject($v, $e);
	  } else {
		$this->transmuteScalar($v, $e);
	  }
	}

	$this->current_node = $t_node;
  }



  /**
   * Transmute Transmutable objects
   *
   * @param Transmutable $transmutable object implementing the Transmutable interface to be serialized
   * @param DOMElement $e XML DOM parent element
   *
   */
  public function transmuteObject($p_value, $e) { 
	$t_node = $this->current_node;

	// set entity node within current passed element
	$this->current_node = $e->appendChild($this->xml_obj->createElement('entity'));	

	// transmute object and reset current node
	$this->transmuteRoot($p_value);
	$this->current_node = $t_node;
  }


  /**
   * Populate representation from an transmutable object
   *
   * @param Transmutable $transmutable object implementing the Transmutable interface
   *
   */
  public function transmuteRoot($transmutable) { 
	// if current_node is null, assume this is a document-level
	// entity node, otherwise it is a sub-level entity node
	if (is_null($this->current_node)) 
	  $this->current_node = $this->xml_root->appendChild($this->xml_obj->createElement('entity'));

	$this->current_node->setAttribute('type', strtolower(get_class($transmutable)));

	$transmutable->transmuteTo($this);
  }


  /**
   * Reset append axis to document-level. Call this method
   * after each transmuteRoot() call for writing mutliple entities
   *
   */
  public function reset() { 
	$this->current_node = null;
  }


  /**
   * Write serialization to stdout
   *
   */
  public function flush() { 
	return $this->xml_obj->saveXML();
  }



  /* TRANSMUTE ENTITY FROM XML */

  /**
   * Type specific method to load entities from a string
   *
   * @param string $entityStr string representation of the entities to be parsed
   *
   */
  public function parse($entityStr) {
	$this->xml_obj->loadXML($entityStr);
	$entities = $this->xml_obj->getElementsByTagName('entities');

	if ($entities->length == 1) {
	  // There should be exactly 1 entities tag
	  $this->xml_root = $entities->item(0);
	} else {
	  // If not, throw an exception
	}
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
   * Recursively convert XML to a PHP variable
   *
   * @param $e Element to be transmuted
   *
   */
  public function untransmuteValue($e) {
	if (is_null($e) || !$e->hasChildNodes()) {
	  // If the item does not exist or has no content, return null
	  return null;
	} elseif ($e->firstChild->nodeType == XML_TEXT_NODE) {
	  // If the first (assumed only) child is a text node, convert to scalar
	  return $e->firstChild->wholeText;
	} elseif ($e->firstChild->tagName == 'element') {
	  // If the first (assumed all) child has name 'element', convert to array
	  $elements = $e->childNodes;
	  $eArray = array();
	  foreach ($elements as $el) {
		$index = $el->getAttribute('index');
		if (is_numeric($index)) { $eArray[$index] = $this->untransmuteValue($el); }
		else { $eArray[] = $this->untransmuteValue($el); }
	  }

	  return $eArray;
	} else {
	  // If the first (assumed all) child is a non-element node, convert to associative array
	  $elements = $e->childNodes;
	  $eArray = array();
	  foreach ($elements as $el) {
		$index = $el->tagName;
		$eArray[$index] = $this->untransmuteValue($el);
	  }

	  return $eArray;
	}
  }


  /**
   * Move to next entity in the loaded XML document; call this function before each
   *  call to untransmuteRoot to read multiple entities
   *
   */
  public function next() {
	if (is_null($this->current_node)) {
	  $entities = $this->xml_root->getElementsByTagName('entity');
	  $this->current_node = $entities->item(0);
	} else {
	  $this->current_node = $this->current_node->nextSibling;
	}
	 
	if (is_null($this->current_node)) return false;
	return true;
  }

}