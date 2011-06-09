<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('IDENTITY_GETALL',		'SELECT person.* FROM person');

define('IDENTITY_GETBYID',		'SELECT person.* FROM person
					WHERE personID = ?');

define('IDENTITY_GETBYFILTER_STUB',	'SELECT DISTINCT person.* FROM person
					NATURAL LEFT JOIN nameAlias');

define('ALIAS_GETBYIDENTITY',		'SELECT DISTINCT nameAlias FROM nameAlias
					WHERE personID = ?');


/* UPDATE STATEMENTS */

define('IDENTITY_INSERT',		'INSERT INTO person
					SET prefix = ?, firstName = ?, middleName = ?,
					lastName = ?, suffix = ?, preferredName = ?,
					primaryEmail = ?, title = ?, optOut = ?');

define('ALIAS_INSERT',			'INSERT INTO nameAlias
					SET personID = ?, nameAlias = ?');

define('ALIAS_DELETE',			'DELETE FROM nameAlias
					WHERE personID = ?');
