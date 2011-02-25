<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('CONTACT_GETALL',		'SELECT contactInfo.* FROM contactInfo');

define('CONTACT_GETBYID',		'SELECT contactInfo.* FROM contactInfo WHERE contactInfoID = ?');

define('CONTACT_GETBYFILTER_STUB',	'SELECT contactInfo.* FROM contactInfo NATURAL JOIN site');

define('FIELD_GETBYCONTACT',		'SELECT contactInfoFieldType, value, sortOrder, isRepeatable, 
						validationExpression, emlType
					FROM contactInfoField NATURAL JOIN contactInfoFieldType
					WHERE contactInfoID = ?
					ORDER BY contactInfoFieldType, sortOrder ASC');

define('FIELDTYPE_GETBYNAME',		'SELECT contactInfoFieldType.* FROM contactInfoField
					WHERE contactInfoField = ?');


/* UPDATE STATEMENTS */