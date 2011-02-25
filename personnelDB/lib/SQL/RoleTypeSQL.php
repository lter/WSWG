<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('ROLETYPE_GETALL_NSF',		'SELECT nsfRoleType.*, "nsf" as type
					FROM nsfRoleType');

define('ROLETYPE_GETALL_LOCAL',		'SELECT localRoleType.*, "local" as type
					FROM localRoleType');

define('ROLETYPE_GETBYID_NSF',		'SELECT nsfRoleType.*, "nsf" as type
					FROM nsfRoleType WHERE nsfRoleTypeID = ?');

define('ROLETYPE_GETBYID_LOCAL',	'SELECT localRoleType.*, "local" as type
					FROM localRoleType WHERE localRoleTypeID = ?');

define('ROLETYPE_GETBYFILTER_NSF_STUB',	'SELECT nsfRoleType.*, "nsf" as type
					FROM nsfRoleType JOIN site ON (false)');

define('ROLETYPE_GETBYFILTER_LOCAL_STUB','SELECT localRoleType.*, "local" as type
					FROM localRoleType JOIN site USING (siteID)');

/* UPDATE STATEMENTS */
