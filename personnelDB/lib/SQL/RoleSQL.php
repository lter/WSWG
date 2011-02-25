<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('ROLE_GETALL_NSF',		'SELECT nsfRole.*, "nsf" as type FROM nsfRole');

define('ROLE_GETALL_LOCAL',		'SELECT localRole.*, "local" as type FROM localRole');

define('ROLE_GETBYID_NSF',		'SELECT nsfRole.*, "nsf" as type
					FROM nsfRole WHERE nsfRoleID = ?');

define('ROLE_GETBYID_LOCAL',		'SELECT localRole.*, "local" as type
					FROM localRole WHERE localRoleID = ?');

define('ROLE_GETBYFILTER_NSF_STUB',	'SELECT nsfRole.*, "nsf" as type
					FROM nsfRole JOIN site USING (siteID)
						JOIN nsfRoleType ON (roleTypeID = nsfRoleTypeID)
						JOIN person USING (personID)
						NATURAL LEFT JOIN nameAlias');

define('ROLE_GETBYFILTER_LOCAL_STUB',	'SELECT localRole.*, "local" as type
					FROM localRole JOIN site USING (siteID)
						JOIN localRoleType ON (roleTypeID = localRoleTypeID)
						JOIN person USING (personID)
						NATURAL LEFT JOIN nameAlias');


/* UPDATE STATEMENTS */
