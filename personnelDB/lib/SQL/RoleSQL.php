<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('ROLE_GETALL_NSF',		'SELECT nsfRole.*, "nsf" as type FROM nsfRole');

define('ROLE_GETALL_LOCAL',		'SELECT localRole.*, "local" as type as type FROM localRole');

define('ROLE_GETALL',			ROLE_GETALL_NSF.' UNION '.ROLE_GETALL_LOCAL);

define('ROLE_GETBYID_NSF',		'SELECT nsfRole.*, "nsf" as type
					FROM nsfRole WHERE nsfRoleID = ?');

define('ROLE_GETBYID_LOCAL',		'SELECT localRole.*, "local" as type
					FROM localRole WHERE localRoleID = ?');

define('ROLE_GETBYFILTER_NSF_STUB',	'SELECT nsfRole.*, "nsf" as type, site
					FROM nfsRole JOIN person USING (personID)
						JOIN site USING (siteID)
						JOIN nsfRoleType ON (roleTypeID = nsfRoleTypeID)');

define('ROLE_GETBYFILTER_LOCAL_STUB',	'SELECT localRole.*, "local" as type
					FROM localRole JOIN person USING (personID)
						JOIN site USING (siteID)
						JOIN localRoleType ON (roleTypeID = localRoleTypeID)');

define('ROLE_GETBYFILTER_STUB',		'SELECT * FROM
					('.ROLE_GETBYFILTER_NSF_STUB.' UNION '.ROLE_GETBYFILTER_LOCAL_STUB.')
					as role')');


/* UPDATE STATEMENTS */
