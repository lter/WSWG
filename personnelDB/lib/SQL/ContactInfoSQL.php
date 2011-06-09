<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('CONTACT_GETALL',		'SELECT contactInfo.* FROM contactInfo');

define('CONTACT_GETBYID',		'SELECT contactInfo.* FROM contactInfo WHERE contactInfoID = ?');

define('CONTACT_GETBYFILTER_STUB',	'SELECT contactInfo.* FROM contactInfo
						NATURAL JOIN site
						NATURAL JOIN person');

define('FIELD_GETBYCONTACT',		'SELECT contactInfoFieldTypeID, contactInfoFieldType, value,
						sortOrder, isRepeatable, validationExpression, emlType
					FROM contactInfoField NATURAL JOIN contactInfoFieldType
					WHERE contactInfoID = ?
					ORDER BY contactInfoFieldType, sortOrder ASC');

define('FIELDTYPE_GETBYNAME',		'SELECT contactInfoFieldType.* FROM contactInfoFieldType
					WHERE contactInfoFieldType = ?');


/* UPDATE STATEMENTS */

define('CONTACT_INSERT',		'INSERT INTO contactInfo
					SET personID = ?, siteID = ?, label = ?, isPrimary = ?,
					beginDate = ?, endDate = ?, isActive = ?');

define('FIELD_INSERT',			'INSERT INTO contactInfoField
					SET contactInfoID = ?, contactInfoFieldTypeID = ?,
					value = ?, sortOrder = ?');

define('FIELD_DELETE',			'DELETE FROM contactInfoField
					WHERE contactInfoID = ?');
