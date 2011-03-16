<?php

use \PersonnelDB\PersonnelDB;

/**
 * REST Server Utility functions
 */


/**
 * Get correct Transmuter object for content-type
 *
 * @param string $ctype Content-type value
 * @return Transmuter $trans_obj Transmuter object
 *
 */
function getTransmuter($ctype) { 
  switch ($ctype) {
  case 'text/plain':
    $trans_obj = new TextTransmuter();
    break;
  case 'application/json':
    $trans_obj = new JSONTransmuter();
    break;
  case 'text/xml':
    $trans_obj = new XMLTransmuter();
    break;
  default:
    $trans_obj = new XMLTransmuter();
  }
  return $trans_obj;
}


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

function authorize($server) {
  $personneldb =& PersonnelDB::getInstance();
  $login = $personneldb->LoginStore->getBySignature($server->params['sig']);

  if ($login === null) {
    // Exit with access denied error
    $server->dieRespond(UNAUTHORIZED, 'Signature was not included or was invalid');
  } else {
    return $login;
  }
}
