<?php

use \PersonnelDB\PersonnelDB;

/**
 * REST request handlers
 * the general format is function(server, args) where the server is the RESTServer object and the args are the
 * matches that are found in the regular expression that the hander has been registered with
 */

/* 
 * Get all entities 
 * args should have the name of the entity that has been requested
 */
function getEntity($server, $args) { 
  // get args
  list($e_name) = $args;

  $personnel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));

  // Get all entities from the store
  $entities = $personnel->$store_name->getAll();

  // return serialized output
  return serializeEntities($entities, $server->contentType);
}

/*
 * Get a new entity
 * args should be the name of the entity requested. The name is used to find the appropriate store
 */
function getEntityBlank($server, $args) { 
  // get args
  list($e_name) = $args;

  $personnel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
    
  // Get a blank entity
  $entity = $personnel->$store_name->getEmpty();

  // return serialized output
  return serializeEntities(array($entity), $server->contentType);
}

/*
 * Get a list of enties by id
 * args should be one or more id number separated by commas
 */
function getEntityById($server, $args) { 
  // get args
  list($e_name, $idstr) = $args;

  $personnel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
  $entities = array();

  // get entity objects for set of ids passed
  $ids = array_unique(explode(',', $idstr));    
  foreach ($ids as $id) {
	if ($entity = $personnel->$store_name->getById($id)) {
	  $entities[] = $entity;
	}
  }

  // return serialized output
  return serializeEntities($entities, $server->contentType);
}


// NYI
function getRoleByType($server, $args) {

}

// NYI
function getRoleById($server, $args) {

}

/*
function postentity($server, $args) {
  $login = authorize($server);

  // get args
  list($e_name) = $args;

  $personnel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
  $trans_obj = getTransmuter($server->contentType);

  // Untransmute entity and write to database
  $entity = $personnel->$store_name->getEmpty();
  $trans_obj->parse($server->body);
  $trans_obj->next();
  $trans_obj->untransmuteRoot($entity);
  $entity = $personnel->$store_name->put($entity);

  // Create ChangeLog entry
  $cl = $personnel->ChangeLogStore->getEmpty();
  $cl->entityType = get_class($entity);
  $cl->entityId = $entity->uniqueId;
  $cl->signature = $login->signature;

  $personnel->ChangeLogStore->put($cl);

  // return serialized output
  return serializeEntities(array($entity), $server->contentType);
}


function putentity($server, $args) {
  $login = authorize($server);

  // get args
  list($e_name, $id) = $args;

  $personnel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
  $trans_obj = getTransmuter($server->contentType);

  // Get snapshot of existing entity
  $snap = snapshot($id, $store_name);

  // Untransmute entity and write to database
  $entity = $personnel->$store_name->getEmpty();
  $trans_obj->parse($server->body);
  $trans_obj->next();
  $trans_obj->untransmuteRoot($entity);
  $entity = $personnel->$store_name->put($entity);

  // Create ChangeLog entry
  $cl = $personnel->ChangeLogStore->getEmpty();
  $cl->entityType = get_class($entity);
  $cl->entityId = $entity->uniqueId;
  $cl->signature = $login->signature;
  $cl->snapshot = $snap;

  $personnel->ChangeLogStore->put($cl);
  
  // Return serialized output
  return serializeEntities(array($entity), $server->contentType);
}
*/

?>
