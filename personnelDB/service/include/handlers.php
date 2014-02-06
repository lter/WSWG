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
 * Get a list of enties by acronym
 * args should be one or more id number separated by commas
 */
function getEntityByAcronym($server, $args) { 
  // get args
  list($e_name, $idstr) = $args;

  $personnel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
  $entities = array();

  // get entity objects for set of ids passed
  $ids = array_unique(explode(',', $idstr));
  
  foreach ($ids as $id) {
    if ($entity = $personnel->$store_name->getByAcronym($id)) {
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


/*
 * Get a list of roles by ids and type (nsf or local)
 */
function getRoleByUserId($server, $args) {
  
  global $tm;
  $tm->tv("Made It To ROLE BY USER Functionality");
  $tm->tv("ARGS: ".print_r($args, true));
  
  // get args
  list($e_name, $idstr) = $args;
  
  $tm->tv("IDSTR: ".$idstr);
  $tm->tv("E NAME: ".$e_name);
  
  $personnel =& PersonnelDB::getInstance();
  $store_name = getEntityStore(strtolower($e_name));
  $entities = array();
  
  
  $tm->tv("STORE NAME: ".$store_name);
  
  
  // get entity objects for set of ids passed
  $ids = array_unique(explode(',', $idstr));    
  foreach ($ids as $id) {
    //$entity = $personnel->$store_name->getByUserId($id);
	//$tm->tv("USER ENTITY (SINGULAR): ".print_r($entity, true));
    if ($entity = $personnel->$store_name->getByUserId($id)) {
      $tm->tv("USER ENTITY (SINGULAR): ".print_r($entity, true));
      $entities[] = $entity;
    }
  }

  $tm->tv("USER ENTITIES: ".print_r($entities, true));
  
  $tm->tv("USER ENTITIES1: ".$entity->personID);
  
  $r_type = 'nsf';
  $entity_id = $entity->personID;
  $entities = array();
  
  $entities = $personnel->RoleStore->getByFilter(array('personID' => $entity_id));
  
  //$store_name = 'RoleStore';
  
  //foreach ($ids as $id) {
  //  if ($entity = $personnel->$store_name->getRoles()) {
  //    $entities[] = $entity;
  //  }
  //}
	
  //$entities = getRoles();

  // return serialized output
  return serializeEntities($entities, $server->contentType);
  
  
  //// get entity objects for set of ids passed
  //$ids = array_unique(explode(',', $idstr));    
  //foreach ($ids as $id) {
  //  if ($entity = $personnel->$store_name->getByUserId($id, $r_type)) {
  //    $entities[] = $entity;
  //  }
  //}
  //
  //// return serialized output
  //return serializeEntities($entities, $server->contentType);
}






// ************************************ { 2013-08-22 - RC } ************************************
// CREATES A NEW PERSONNELDB RECORD
// IT WILL INSERT A NEW RECORD AND ALSO TRY TO CREATE AN LDAP ENTRY FOR THAT USER
// *********************************************************************************************
function addEntity($server, $args) {



	global $tm;



	// ************************************ { 2013-08-22 - RC } ************************************
	// CHECK TO SEE IF THE USER IS AUTHORIZED TO CREATE A NEW RECORD FOR THIS SITE
	// FUNCTION LIVES IN /var/www/services/personnelDB/include/utils.php
	// *********************************************************************************************
	$login = authorize($server);






	// ************************************ { 2013-08-22 - RC } ************************************
	// GET THE STORE NAME FROM 'server.php'
	// *********************************************************************************************
	list($e_name) = $args;






	// ************************************ { 2013-08-22 - RC } ************************************
	// CREATE A NEW DATABASE CONNECTION
	// *********************************************************************************************
	$personnel =& PersonnelDB::getInstance();
	$store_name = getEntityStore(strtolower($e_name)); // FUNCTION LIVES IN /var/www/services/personnelDB/include/utils.php
	$newEntities = array();






	// ************************************ { 2013-08-22 - RC } ************************************
	// CHECK TO SEE IF THE USER IS LOGGED IN (OR HAS NECESSARY PRIVS) ... IF NOT, RETURN AN ERROR
	// *********************************************************************************************
	if (!$login) {
		$message = array(
			"errors" => array(
				"error" => "Authorization Is Required.",
				"instructions" => "Adding new PersonnelDB entries requires an authorization header to be sent along with the information.  Either no authorization header was sent, an authorization header was sent, but the credentials were incorrect or an authorization header was sent, but the user does not have sufficient privileges to update other users."
			)
		);
		$xml_doc = $personnel->build_xml_string($message);
		return $xml_doc->saveXML();
		exit;
	}





	// ************************************ { 2013-08-22 - RC } ************************************
	// UNTRANSMUTE ENTITY AND WRITE TO DATABASE
	// FUNCTION LIVES IN /var/www/services/personnelDB/include/utils.php
	// *********************************************************************************************
	$entities = unserializeEntities($server->body, strtolower($e_name));






	// ************************************ { 2013-04-27 - RC } ************************************
	// PULL OUT SOME INFORMATION FROM THE SUBMITTED XML
	// *********************************************************************************************
	if (preg_match('/<primaryEmail>(.*)<\/primaryEmail>/', $server->body, $matches)) {
		$email = $matches[1];
	}

	if (preg_match('/<firstName>(.*)<\/firstName>/', $server->body, $matches)) {
		$firstName = $matches[1];
	}

	if (preg_match('/<lastName>(.*)<\/lastName>/', $server->body, $matches)) {
		$lastName = $matches[1];
	}





	// ************************************ { 2013-08-22 - RC } ************************************
	// CHECK TO SEE IF THEY AT LEAST SUBMITTED THE BARE MINIMUM FIELDS ... RETURN AN ERROR IF NOT
	// *********************************************************************************************
	if ((!$email) || (!$firstName) || (!$lastName)) {

		$message = array(
			"errors" => array(
				"error" => "First Name, Last Name And Email Are Required.",
				"instructions" => "Please try submitting the form again with the required fields."
			)
		);
		$xml_doc = $personnel->build_xml_string($message);

		return $xml_doc->saveXML();

		exit;

	}






	// ************************************ { 2013-08-22 - RC } ************************************
	// CHECK FOR DUPLICATE RECORDS SO WE DO NOT CREATE TWO RECORDS WITH THE SAME INFORMATION
	// *********************************************************************************************
	$dupes = $personnel->IdentityStore->checkDupes($email, $firstName, $lastName);
 
	if ($dupes->personID) {

		$error = "That Person Already Exists In This Database.";

		$message = array(
			"errors" => array(
				"personID" => $dupes->personID, 
				"error" => "That Person Already Exists In This Database.",
				"instructions" => "Please try updating the record instead of inserting it."
			)
		);

		$xml_doc = $personnel->build_xml_string($message);
		$xml_doc = $xml_doc;

		return $xml_doc->saveXML();

		exit;

	} 






	// ************************************ { 2013-08-22 - RC } ************************************
	// LOOP THROUGH THE XML AND INSERT THE RECORD INTO THE DB
	// *********************************************************************************************
	foreach ($entities as $e) {
		$entity = $personnel->$store_name->insert($e);
		$newEntities[] = $entity;
	}






	// ************************************ { 2013-08-22 - RC } ************************************
	// GET THE NEWLY CREATED PERSON ID SO WE CAN PASS IT TO THE LDAP FUNCTION
	// *********************************************************************************************
	$person_id = $newEntities[0]->personID;





	// ************************************ { 2013-08-22 - RC } ************************************
	// ATTEMPT TO CREATE THE ENTRY IN LDAP
	// FUNCTION LIVES IN /var/www/services/personnelDB/include/utils.php
	// *********************************************************************************************
	if ($message = ldap_insert($person_id, $server->body)) {



		if ($message['success']) {
			$tm->tv('LDAP UPDATE WAS SUCCESSFUL: '.print_r($message, true));
		}
		else {
			$tm->tv('LDAP UPDATE WAS NOT SUCCESSFUL: '.print_r($message, true));
		}



	}
	else {
		$tm->tv('LDAP UPDATE WAS NOT SUCCESSFUL - NO MESSAGE WAS RETURNED');
	}






	// ************************************ { 2013-08-22 - RC } ************************************
	// RETURN SERIALIZED OUTPUT
	// *********************************************************************************************
	return serializeEntities($newEntities, $server->contentType);



}






// ************************************ { 2013-08-22 - RC } ************************************
// UPDATES AN EXISTING PERSONNELDB RECORD
// IT WILL UPDATE THE DATABASE AND ALSO TRY TO UPDATE THE LDAP RECORD FOR THAT USER
// *********************************************************************************************
function updateEntity($server, $args) {



	global $tm;



	// ************************************ { 2013-08-22 - RC } ************************************
	// CHECK TO SEE IF THE USER IS AUTHORIZED TO CREATE A NEW RECORD FOR THIS SITE
	// FUNCTION LIVES IN /var/www/services/personnelDB/include/utils.php
	// *********************************************************************************************
	$login = authorize($server);






	// ************************************ { 2013-08-22 - RC } ************************************
	// GET THE STORE NAME FROM 'server.php'
	// *********************************************************************************************
	list($e_name) = $args;






	// ************************************ { 2013-08-22 - RC } ************************************
	// LOAD THE XML INTO SIMPLEXML SO WE CAN WORK WITH ITS INDIVIDUAL PARTS
	// *********************************************************************************************
	$xml = simplexml_load_string($server->body);
	$person_id = (string)$xml->person->personID;
	$update_user_id = (string)$xml->person->identity->ldapUser;
	$tm->tv('XML: '.print_r($xml, true));






	// ************************************ { 2013-08-22 - RC } ************************************
	// CREATE A NEW DATABASE INSTANCE AND GET THE USERID FOR THAT RECORD (FOR LDAP)
	// *********************************************************************************************
	$personnel =& PersonnelDB::getInstance();
	$person_results = $personnel->PersonStore->getUserById($person_id);
	$person_user_id = $person_results->userID;

	$store_name = getEntityStore(strtolower($e_name)); // FUNCTION LIVES IN /var/www/services/personnelDB/include/utils.php
	$updEntities = array();






	// ************************************ { 2013-08-22 - RC } ************************************
	// CHECK TO SEE IF THE USER IS LOGGED IN (OR HAS NECESSARY PRIVS) ... IF NOT, RETURN AN ERROR
	// *********************************************************************************************
	if (!$login) {
		$message = array(
			"errors" => array(
				"error" => "Authorization Is Required.",
				"instructions" => "Updating existing PersonnelDB entries requires an authorization header to be sent along with the information.  Either no authorization header was sent, an authorization header was sent, but the credentials were incorrect or an authorization header was sent, but the user does not have sufficient privileges to update other users."
			)
		);
		$xml_doc = $personnel->build_xml_string($message);
		return $xml_doc->saveXML();
		exit;
	}






	// ************************************ { 2013-08-22 - RC } ************************************
	// UNTRANSMUTE ENTITY AND WRITE TO DATABASE
	// FUNCTION LIVES IN /var/www/services/personnelDB/include/utils.php
	// *********************************************************************************************
	$entities = unserializeEntities($server->body, strtolower($e_name));






	// ************************************ { 2013-08-22 - RC } ************************************
	// LOOP THROUGH THE XML AND MODIFY THE RECORD IN THE DB
	// *********************************************************************************************
	foreach ($entities as $e) {



		// ************************************ { 2013-08-27 - RC } ************************************
		// IF NO USER ID IS PASSED, GRAB THE EXISTING ONE FROM THE DB SO THAT WE DON'T OVERWRITE IT 
		// - WITH A BLANK
		// *********************************************************************************************
		if (!$e->identity->ldapUser) {
			$e->identity->ldapUser = $person_user_id;
		}

		$entity = $personnel->$store_name->update($e);
		$updEntities[] = $entity;



	}






	// ************************************ { 2013-08-22 - RC } ************************************
	// ATTEMPT TO MODIFY THE ENTRY IN LDAP
	// FUNCTION LIVES IN /var/www/services/personnelDB/include/utils.php
	// *********************************************************************************************
	if ($message = ldap_update($person_user_id, $server->body)) {



		if ($message['success']) {
			$tm->tv('LDAP UPDATE WAS SUCCESSFUL: '.print_r($message, true));
		}
		else {
			$tm->tv('LDAP UPDATE WAS NOT SUCCESSFUL: '.print_r($message, true));
		}



	}
	else {
		$tm->tv('LDAP UPDATE WAS NOT SUCCESSFUL - NO MESSAGE WAS RETURNED');
	}






	// ************************************ { 2013-08-22 - RC } ************************************
	// RETURN SERIALIZED OUTPUT
	// *********************************************************************************************
	return serializeEntities($updEntities, $server->contentType);



}



?>
