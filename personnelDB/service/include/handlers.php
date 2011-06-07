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

  if (!empty($server->params)) {
    // If there are params, use them for filtering
    $entities = $personnel->$store_name->getByFilter($server->params);
  } else {
    // Otherwise, get all entities
    $entities = $personnel->$store_name->getAll();
  }

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

/*
 * Get a list of roles by type (nsf or local)
 */
function getRoleByType($server, $args) {
  // get args
  list($e_name, $r_type) = $args;

  $personnel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));

  $entities = $personnel->$store_name->getByType($r_type);

  // return serialized output
  return serializeEntities($entities, $server->contentType);
}

/*
 * Get a list of roles by ids and type (nsf or local)
 */
function getRoleById($server, $args) {
  // get args
  list($e_name, $r_type, $idstr) = $args;

  $personnel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
  $entities = array();

  // get entity objects for set of ids passed
  $ids = array_unique(explode(',', $idstr));    
  foreach ($ids as $id) {
    if ($entity = $personnel->$store_name->getById($id, $r_type)) {
      $entities[] = $entity;
    }
  }

  // return serialized output
  return serializeEntities($entities, $server->contentType);
}

function addEntity($server, $args) {
  $login = authorize($server);

  // get args
  list($e_name) = $args;

  $personnel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
  $newEntities = array();

  // Untransmute entity and write to database
  $entities = unserializeEntities($server->body, $store_name);
  foreach ($entities as $e) {
    $entity = $personnel->$store_name->put($e);
    $newEntities[] = $entity;
  }

  // return serialized output
  return serializeEntities($newEntities), $server->contentType);
}

function updateEntity($server, $args) {
  $login = authorize($server);

  // get args
  list($e_name, $id) = $args;

  $personnel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
  $updEntities = array();

  // Untransmute entity and write to database
  $entities = unserializeEntities($server->body, $store_name);
  foreach ($entities as $e) {
    $entity = $personnel->$store_name->put($e);
    $updEntities[] = $entity;
  }

  // return serialized output
  return serializeEntities($updEntities, $server->contentType);
}

?>
