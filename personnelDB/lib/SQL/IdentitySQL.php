<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('IDENTITY_GETALL',		'SELECT personID FROM person');

define('IDENTITY_GETBYID',		'SELECT personID FROM person WHERE personID = ?');

define('IDENTITY_GETALIAS',		'SELECT DISTINCT nameAlias FROM nameAlias WHERE personID = ?');

define('IDENTITY_GETBYFILTER_STUB',	'SELECT personID FROM person NATURAL JOIN nameAlias');


/* UPDATE STATEMENTS */