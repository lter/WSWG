<?php

use \PersonnelDB\PersonnelDB;

/**
 * REST Server Utility functions
 */

/**
 * Get correct entity store name for entity key
 *
 * @param string $ename entity name from request URL
 * @return string $store Entity store name
 *
 */
function getEntityStore($ename) { 
  switch ($ename) { 
  case 'person':
    $store = 'PersonStore';
    break;
  case 'identity':
    $store = 'IdentityStore';
    break;
  case 'contact':
    $store = 'ContactInfoStore';
    break;
  case 'role':
    $store = 'RoleStore';
    break;
  case 'roletype':
    $store = 'RoleTypeStore';
    break;
  case 'site':
    $store = 'SiteStore';
    break;
  default:
    $store = null;
  }
  return $store;
}

function serializeEntities($entities, $content) {
  switch ($content) {
  case 'text/xml':
    $personnel =& PersonnelDB::getInstance();
    $xml_doc = $personnel->to_xml($entities);
    return $xml_doc->saveXML();
    break;
  case 'application/json':
    break;
  }
}

function unserializeEntities($xml, $entityType) {
  $personnel =& PersonnelDB::getInstance();
  $xml_doc = new DOMDocument();
  $xml_doc->loadXML($xml);

  return $personnel->from_xml($xml_doc, $entityType);
}

function authorize($server) {
  return true;
}
