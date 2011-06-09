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
						JOIN nsfRoleType ON (roleTypeID = nsfRoleTypeID)');

define('ROLE_GETBYFILTER_LOCAL_STUB',	'SELECT localRole.*, "local" as type FROM localRole
						NATURAL JOIN site
						NATURAL JOIN person
						JOIN localRoleType ON (roleTypeID = localRoleTypeID)');


/* UPDATE STATEMENTS */

define('ROLE_INSERT_NSF',		'INSERT INTO nsfRole
					SET personID = ?, roleTypeID = ?, siteID = ?,
					beginDate = ?, endDate = ?, isActive = ?');

define('ROLE_INSERT_LOCAL',		'INSERT INTO localRole
					SET personID = ?, roleTypeID = ?, siteID = ?,
					beginDate = ?, endDate = ?, isActive = ?');
