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
					FROM localRoleType NATURAL JOIN site');

/* UPDATE STATEMENTS */

define('ROLETYPE_INSERT_LOCAL',		'INSERT INTO localRoleType
					SET siteID = ?, roleName = ?, isRepeatable = ?');

define('ROLETYPE_INSERT_NSF',		'INSERT INTO nsfRoleType
					SET roleName = ?, isRepeatable = ?');

define('ROLETYPE_DELETE_LOCAL',		'DELETE FROM localRoleType
					WHERE localRoleTypeID = ?');