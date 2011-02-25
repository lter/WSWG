<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('PERSON_GETALL',			'SELECT personID, changeDate FROM person');

define('PERSON_GETBYID',		'SELECT personID, changeDate FROM person WHERE personID = ?');

define('PERSON_GETBYFILTER_STUB',	'SELECT DISTINCT personID, changeDate FROM person
						NATURAL LEFT JOIN nameAlias
						LEFT JOIN nsfRole USING (personID)
						LEFT JOIN nsfRoleType ON (nsfRole.roleTypeID = nsfRoleTypeID)
						LEFT JOIN site as s1 ON (nsfRole.siteID = s1.siteID)
						LEFT JOIN localRole USING (personID)
						LEFT JOIN localRoleType ON (localRole.roleTypeID = nsfRoleTypeID)
						LEFT JOIN site as s2 ON (localRole.siteID = s2.siteID)');


/* UPDATE STATEMENTS */