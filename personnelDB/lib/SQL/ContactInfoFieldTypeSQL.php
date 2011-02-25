<?php

namespace PersonnelDB;

/* ACCESS STATEMENTS */

define('CONTACT_INFO_FIELD_TYPE_GETALL',		'SELECT * FROM contactInfoFieldType');

define('CONTACT_INFO_FIELD_TYPE_GETBYID',		'SELECT * FROM contactInfoFieldType WHERE contactInfoFieldTypeID= ?');

define('CONTACT_INFO_FIELD_TYPE_GETBYTYPE', 'Select * from contactInfoFieldType where contactInfoFieldType = ?') 

define('CONTACT_INFO_FIELD_TYPE_GETBYFILTER_STUB',	'SELECT DISTINCT person.* FROM person NATURAL LEFT JOIN nameAlias');


/* UPDATE STATEMENTS */
