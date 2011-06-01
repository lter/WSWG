<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('ROLE_GETALL_NSF',		'SELECT nsfRole.*, "nsf" as type FROM nsfRole');

define('ROLE_GETALL_LOCAL',		'SELECT localRole.*, "local" as type FROM localRole');

define('ROLE_GETBYID_NSF',		'SELECT nsfRole.*, "nsf" as type
					FROM nsfRole WHERE roleID = ?');

define('ROLE_GETBYID_LOCAL',		'SELECT localRole.*, "local" as type
					FROM localRole WHERE roleID = ?');

define('ROLE_GETBYFILTER_NSF_STUB',	'SELECT nsfRole.*, "nsf" as type FROM nsfRole
						NATURAL JOIN site
						NATURAL JOIN person
						NATURAL LEFT JOIN nameAlias
						JOIN nsfRoleType ON (roleTypeID = nsfRoleTypeID)');

define('ROLE_GETBYFILTER_LOCAL_STUB',	'SELECT localRole.*, "local" as type FROM localRole
						NATURAL JOIN site
						NATURAL JOIN person
						NATURAL LEFT JOIN nameAlias
						JOIN localRoleType ON (roleTypeID = localRoleTypeID)');


/* UPDATE STATEMENTS */
