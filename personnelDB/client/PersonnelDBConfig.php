<?php

// PersonnelDBConfig.php
//  Configuration options for the PersonnelDB interface

// Web page definitions
define('PDB_URL', 'http://'.$_SERVER['HTTP_HOST'].'/personnelDB/');

// Web service definitions
define('WS_URL', 'http://sunshine.lternet.edu/services/personnelDB/');

// Local file paths
define('ROOT_PATH', '/var/www/personnelDB/');
define('XSL_PATH', ROOT_PATH.'template/xsl/');

// Support functions
require 'lib/ws-functions.php';
require 'lib/xml-functions.php';
