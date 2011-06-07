<?php

namespace PersonnelDB;
use \Exception as Exception;

// Include stores
include('stores/PersonStore.php');
include('stores/ContactInfoStore.php');
include('stores/IdentityStore.php');
include('stores/RoleStore.php');
include('stores/RoleTypeStore.php');
include('stores/SiteStore.php');


class PersonnelDB {

  private static $storeFront;
  private $stores = array();
  private $lists = array();

  /* METHODS */

  private function __construct() {
    // Singleton class, use getInstance
  }

  public function __destruct() {

  }

  // Returns an instance of the PersonnelDB class
  // Use this instead of new
  public static function getInstance() {
    if (!isset(self::$storeFront)) {
      self::$storeFront = new PersonnelDB();
    }
	
    return self::$storeFront;
  }


  /* SERIALIZATION */

  public function to_xml($entities) {
    $xml_doc = new \DOMDocument('1.0','utf-8');

    switch (get_class($entities[0])) {
    case 'PersonnelDB\Person':
      $xml_obj = $xml_doc->appendChild($xml_doc->createElement('personnel'));
      foreach ($entities as $e) $this->import_xml($e, $xml_obj, $xml_doc);
      break;

    case 'PersonnelDB\Identity':
      $xml_obj = $xml_doc->appendChild($xml_doc->createElement('personnel'));
      foreach ($entities as $e) {
	$p = $xml_obj->appendChild($xml_doc->createElement('person'));
	$p->appendChild($xml_doc->createElement('personID', $e->personID));
	$this->import_xml($e, $p, $xml_doc);
      }
      break;

    case 'PersonnelDB\Role':
      $xml_obj = $xml_doc->appendChild($xml_doc->createElement('personnel'));
      foreach ($entities as $e) {
	$l = $this->get_xml_list('roleList', $e->personID, $xml_obj, $xml_doc);
	$this->import_xml($e, $l, $xml_doc);
      }
      break;

    case 'PersonnelDB\ContactInfo':
      $xml_obj = $xml_doc->appendChild($xml_doc->createElement('personnel'));
      foreach ($entities as $e) {
	$l = $this->get_xml_list('contactInfoList', $e->personID, $xml_obj, $xml_doc);
	$this->import_xml($e, $l, $xml_doc);
      }
      break;

    case 'PersonnelDB\RoleType':
      $xml_obj = $xml_doc->appendChild($xml_doc->createElement('roleTypeList'));
      foreach ($entities as $e) $this->import_xml($e, $xml_obj, $xml_doc);
      break;

    case 'PersonnelDB\Site':
      $xml_obj = $xml_doc->appendChild($xml_doc->createElement('siteList'));
      foreach ($entities as $e) $this->import_xml($e, $xml_obj, $xml_doc);
      break;
    }

    return $xml_doc;
  }

  public function from_xml($xml_doc, $entityType) {
    switch ($entityType) {
    case 'person':
      return $this->find_fragments($xml_doc, 'person', $this->PersonStore);
      break;

    case 'roletype':
      return $this->find_fragments($xml_doc, 'roleType', $this->RoleTypeStore);
      break;
    }
  }

  private function import_xml($e, $xml_obj, $xml_doc) {
    $fragment = $xml_doc->importNode($e->to_xml_fragment(), TRUE);
    $xml_obj->appendChild($fragment);
  }

  private function get_xml_list($type, $personID, $xml_obj, $xml_doc) {
    if (!array_key_exists($type, $this->lists)) { $this->lists[$type] = array(); }

    if (!array_key_exists($personID, $this->lists[$type])) {   
      $p = $xml_obj->appendChild($xml_doc->createElement('person'));
      $p->appendChild($xml_doc->createElement('personID', $personID));
      $this->lists[$type][$personID] = $p->appendChild($xml_doc->createElement($type));
    }

    return $this->lists[$type][$personID];
  }

  private function find_fragments($xml_doc, $tagname, $store) {
    $entities = array();

    $xpath = new \DOMXPath($node->ownerDocument);
    $fragments = $xpath->query("*/$tagname");

    foreach ($fragments as $f) {
      $e = $store->getEmpty();
      $e->from_xml_fragment($f);
      $entities[] = $e;
    }
    
    return $entities;
  }

  /* OVERLOADED METHODS */

  // Overload for getting member data
  // Access stores as member data by name
  public function __get($store) {
    $fqStore = __NAMESPACE__."\\".$store;
    if (isset($this->stores[$store])) {
      // If the store has been instantiated, return it
      return $this->stores[$store];
    } elseif (class_exists($fqStore)) {
      // Otherwise, instantiate and return
      $this->stores[$store] = new $fqStore();
      return $this->stores[$store];
    } else {
      // This is not a recognized store class!
      throw new Exception('Attempt to create unknown store type: '.$store);
    }
  }
}