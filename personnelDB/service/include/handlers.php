<?php
  /**
   * REST request handlers
   * the general format is function(server, args) where the server is the RESTServer object and the args are the
   * matches that are found in the regular expression that the hander has been registered with
   */


/* 
 * Get all entities 
 * args should have the name of the entity that has been requested
 */
function getentity($server, $args) { 
  // get args
  list($e_name) = $args;

  $personel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));

  // Get all entities from the store
  $entities = $personel->$store_name->getAll();

  // return serialized output
  return serializeEntities($entities, $server->contentType);
}

/*
 * Get a new entity
 * args should be the name of the entity requested. The name is used to find the appropriate store
 */
function getnewentity($server, $args) { 
  // get args
  list($e_name) = $args;

  $personel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
    
  // Get a blank entity
  $entity = $personel->$store_name->getEmpty();

  // return serialized output
  return serializeEntities(array($entity), $server->contentType);
}

/*
 * Get a list of enties by id
 * args should be one or more id number separated by commas
 */
function getentitybyid($server, $args) { 
  // get args
  list($e_name, $idstr) = $args;

  $personel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
  $entities = array();

  // get entity objects for set of ids passed
  $ids = array_unique(explode(',', $idstr));    
  foreach ($ids as $id) {
	if ($entity = $personel->$store_name->getById($id)) {
	  $entities[] = $entity;
	}
  }

  // return serialized output
  return serializeEntities($entities, $server->contentType);
}


/*
 * Get a list of entries by a filter expression
 * args the name of store and a string
 */
function getentitybyfilter($server, $args) {
  // Get args
  list($e_name, $filter) = $args;

  $personel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));

  // Scan expression, tokenize, execute
  $parser = new FilterParser($filter, $personel, $store_name);
  $parser->tokenize();
  $entities = $parser->evaluate();

  // return serialized output
  return serializeEntities($entities, $server->contentType);
}

function postentity($server, $args) {
  $login = authorize($server);

  // get args
  list($e_name) = $args;

  $personel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
  $trans_obj = getTransmuter($server->contentType);

  // Untransmute entity and write to database
  $entity = $personel->$store_name->getEmpty();
  $trans_obj->parse($server->body);
  $trans_obj->next();
  $trans_obj->untransmuteRoot($entity);
  $entity = $personel->$store_name->put($entity);

  // Create ChangeLog entry
  $cl = $personel->ChangeLogStore->getEmpty();
  $cl->entityType = get_class($entity);
  $cl->entityId = $entity->uniqueId;
  $cl->signature = $login->signature;

  $personel->ChangeLogStore->put($cl);

  // return serialized output
  return serializeEntities(array($entity), $server->contentType);
}


function putentity($server, $args) {
  $login = authorize($server);

  // get args
  list($e_name, $id) = $args;

  $personel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
  $trans_obj = getTransmuter($server->contentType);

  // Get snapshot of existing entity
  $snap = snapshot($id, $store_name);

  // Untransmute entity and write to database
  $entity = $personel->$store_name->getEmpty();
  $trans_obj->parse($server->body);
  $trans_obj->next();
  $trans_obj->untransmuteRoot($entity);
  $entity = $personel->$store_name->put($entity);

  // Create ChangeLog entry
  $cl = $personel->ChangeLogStore->getEmpty();
  $cl->entityType = get_class($entity);
  $cl->entityId = $entity->uniqueId;
  $cl->signature = $login->signature;
  $cl->snapshot = $snap;

  $personel->ChangeLogStore->put($cl);
  
  // Return serialized output
  return serializeEntities(array($entity), $server->contentType);
}

?>
