<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('ROLETYPE_GETALL_NSF',		'SELECT nsfRoleTypeID as roleTypeID, null as scope, roleType,
						isRepeatable, "nsf" as type
					FROM nsfRoleType');

define('ROLETYPE_GETALL_LOCAL',		'SELECT localRoleTypeID as roleTypeID, scope, roleType,
						isRepeatable, "local" as type
					FROM localRoleType');

define('ROLETYPE_GETALL',		ROLETYPE_GETALL_NSF.' UNION '.ROLETYPE_GETALL_LOCAL);

define('ROLETYPE_GETBYID_NSF',		'SELECT nsfRoleTypeID as roleTypeID, null as scope, roleType,
						isRepeatable, "nsf" as type
					FROM nsfRoleType WHERE nsfRoleTypeID = ?');

define('ROLETYPE_GETBYID_LOCAL',	'SELECT localRoleTypeID as roleTypeID, scope, roleType,
						isRepeatable, "local" as type
					FROM localRoleType WHERE localRoleTypeID = ?');

define('ROLETYPE_GETBYFILTER_NSF_STUB',	'SELECT nsfRoleTypeID as roleTypeID, null as scope, roleType,
						isRepeatable, "nsf" as type, null as site, null as siteAcronym
					FROM nsfRoleType');

define('ROLETYPE_GETBYFILTER_LOCAL_STUB','SELECT localRoleTypeID as roleTypeID, scope, roleType,
						isRepeatable, "local" as type, site, siteAcronym
					FROM localRoleType JOIN site ON (scope = siteID)');

define('ROLETYPE_GETBYFILTER_STUB',	'SELECT * FROM
					('.ROLETYPE_GETBYFILTER_NSF_STUB.' UNION '.ROLETYPE_GETBYFILTER_LOCAL_STUB.')
					as roleType');


/* UPDATE STATEMENTS */
