<?php

ini_set('display_errors', 1);

// PersonnelDBConfig.php
//  Configuration options for the PersonnelDB interface

// Web page definitions
define('PDB_URL', 'http://'.$_SERVER['HTTP_HOST'].'/personnelDB/');
define('PDB_SECURE_URL', 'https://'.$_SERVER['HTTP_HOST'].'/personnelDB/secure/');

// Web service definitions
define('WS_URL', 'http://person.lternet.edu/services/personnelDB/');

// Local file paths
define('ROOT_PATH', '/var/www/personnelDB/');
define('XSL_PATH', ROOT_PATH.'template/xsl/');
define('LIB_PATH', '/var/www/lib/');

// Support functions
require 'lib/ws-functions.php';
require 'lib/xml-functions.php';

session_start();
