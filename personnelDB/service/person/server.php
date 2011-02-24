<?php

use \RESTServer\RESTServer;

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
$r_server = new RESTServer('/services/person');


// Register patterns
$r_server->registerHandler('GET', '/^\/(contact|role|site|identity)$/i', 'getentity');
$r_server->registerHandler('GET', '/^\/(contact|role|site|identity)\/_$/i', 'getnewentity');
$r_server->registerHandler('GET', '/^\/(contact|role|site|identity)\/(\d+(,\d+){0,})$/i', 'getentitybyid');
$r_server->registerHandler('GET', '/^\/(contact|role|site|identity)\/(\D.*)$/i', 'getentitybyfilter');

$r_server->registerHandler('POST', '/^\/(contact|identity|role)$/i', 'postentity');
$r_server->registerHandler('PUT', '/^\/(contact|identity|role)\/(\d+)$/i', 'putentity');
$r_server->registerHandler('POST', '/^\/comment$/i', 'postcomment');


// set allowed request methods
//
$r_server->registerMethod('GET');
$r_server->registerMethod('POST');
$r_server->registerMethod('PUT');


// register acceptable content types
//
$r_server->registerContentType('text/xml');
$r_server->registerContentType('application/json');
$r_server->registerContentType('text/plain');

// set cache control
//
$r_server->cacheControl('no-cache');

// call REST server
//
$r_server->processCurrentRequest();

?>
