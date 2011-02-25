<?php
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
    case 'identity':
	$store = 'IdentityStore';
	break;
    case 'role':
	$store = 'RoleStore';
	break;
    case 'site':
	$store = 'SiteStore';
	break;
    case 'contact':
  $store = 'ContactInfoStore';
  break;
    case 'person':
  $store = 'PersonStore';
  break;
    default:
	$store = null;
    }
    return $store;
}


function serializeEntities($entities, $content) {
  switch ($content) {
  case 'xml':
    return serializeAsXML($entities);
    break;
  case 'json':
    return serializeAsJSON($entities);
    break;
  }
}

function serializeAsXML($entities) {
  $xml_obj = new DOMDocument('1.0', 'utf-8');
	$xml_root = $xml_obj->appendChild($xml_obj->createElement('personnel'));
  foreach($entities as $entity) {
    $xml_root->appendChild($entity->to_xml());
  }
}

function serializeAsJSON($entities){

}

function snapshot($id, $store_name) {
  $personneldb =& PersonnelDB::getInstance();
  $trans_array = new TextTransmuter();

  $entity = $personneldb->$store_name->getById($id);
  $trans_array->transmuteRoot($entity);

  return $trans_array->flush();
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
