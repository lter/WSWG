<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('IDENTITY_GETALL',		'SELECT * FROM person');

define('IDENTITY_GETBYID',		'SELECT * FROM person WHERE personID = ?');

define('IDENTITY_GETALIAS',		'SELECT * FROM nameAlias WHERE personID = ?');

define('IDENTITY_GETBYFILTER_STUB',	'SELECT * FROM person');


/* UPDATE STATEMENTS */