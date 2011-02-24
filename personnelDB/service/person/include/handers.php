<?php
  /**
   * REST request handlers
   */


function getentity($server, $args) { 
  // get args
  list($e_name) = $args;

  $personel =& UnitRegistry::getInstance();
  $store_name = getEntityStore(strtolower($e_name));

  // Get all entities from the store
  $entities = $unitreg->$store_name->getAll();

  // return serialized output
  return serializeEntities($entities, $server->contentType);
}

?>
