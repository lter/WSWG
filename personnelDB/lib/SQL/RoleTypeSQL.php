<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('ROLETYPE_GETALL',		'SELECT localRoleTypeID as roleTypeID, scope, roleType,
						isRepeatable, "local" as type
					FROM localRoleType');

define('ROLETYPE_GETALL_NSF',		'SELECT localRoleTypeID as roleTypeID, scope, roleType,
						isRepeatable, "local" as type
					FROM localRoleType');

define('ROLETYPE_GETALL_LOCAL',		'SELECT nsfRoleTypeID as roleTypeID, null as scope, roleType,
						isRepeatable, "nsf" as type
					FROM nsfRoleType
					UNION
					SELECT localRoleTypeID as roleTypeID, scope, roleType,
						isRepeatable, "local" as type
					FROM localRoleType');

define('ROLETYPE_GETBYID_NSF',		'SELECT nsfRoleTypeID as roleTypeID, null as scope, roleType,
						isRepeatable, "nsf" as type
					FROM nsfRoleType WHERE nsfRoleTypeID = ?');

define('ROLETYPE_GETBYID_LOCAL',	'SELECT localRoleTypeID as roleTypeID, scope, roleType,
						isRepeatable, "local" as type
					FROM localRoleType WHERE localRoleTypeID = ?');

define('ROLETYPE_GETBYFILTER_STUB',	'SELECT * FROM (
						SELECT nsfRoleTypeID as roleTypeID, null as scope, roleType,
							isRepeatable, "nsf" as type
						FROM nsfRoleType
						UNION
						SELECT localRoleTypeID as roleTypeID, scope, roleType,
							isRepeatable, "local" as type
						FROM localRoleType
					) as roleType LEFT JOIN site ON (scope = siteID)');

/* UPDATE STATEMENTS */
