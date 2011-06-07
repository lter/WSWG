<?php

use \WSWG\RESTServer;

/**
 * LTER Unit Registry REST server
 *
 * All requests are re-directed to this file. Requests
 * are validated and then mapped to appropriate handlers.
 *
 * 
 *
 * Other files
 *-------------------------------------------------------
 *
 * config.php			Rest server configuration
 * handlers.php			Request handler definitions
 * utils.php			Utility functions
 * FilterParser.php		Class for parsing UR filter strings
 *
 */

include('include/config.php');

// Initialize REST server
//
$r_server = new RESTServer('/services/personnelDB');


// Register patterns - GET
$r_server->registerHandler('GET', '/^\/(person|identity|contact|role|roleType|site)$/i', 'getEntity');
$r_server->registerHandler('GET', '/^\/(person|identity|contact|role|roleType|site)\/_$/i', 'getEntityBlank');
$r_server->registerHandler('GET', '/^\/(person|contact|site|identity)\/(\d+(,\d+){0,})$/i', 'getEntityById');
$r_server->registerHandler('GET', '/^\/(role|roleType)\/(nsf|local)$/i', 'getRoleByType');
$r_server->registerHandler('GET', '/^\/(role|roleType)\/(nsf|local)\/(\d+(,\d+){0,})$/i', 'getRoleById');

// Register patterns - POST
$r_server->registerHandler('POST', '/^\/(person|roleType)$/i', 'addEntity');

// Register patterns - PUT
$r_server->registerHandler('PUT', '/^\/(person)\/(\d+(,\d+){0,})($/i', 'updateEntity');


// set allowed request methods
//
$r_server->registerMethod('GET', 'POST', 'PUT');


// register acceptable content types
//
$r_server->registerContentType('text/xml');


// set cache control
//
$r_server->cacheControl('no-cache');


// call REST server
//
$r_server->processCurrentRequest();

?>
