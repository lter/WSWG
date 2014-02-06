<?php

use \PersonnelDB\PersonnelDB;

/**
 * REST Server Utility functions
 */

/**
 * Get correct entity store name for entity key
 *
 * @param string $ename entity name from request URL
 * @return string $store Entity store name
 *
 */
function getEntityStore($ename) { 
  switch ($ename) { 
  case 'person':
    $store = 'PersonStore';
    break;
  case 'identity':
    $store = 'IdentityStore';
    break;
  case 'contact':
    $store = 'ContactInfoStore';
    break;
  case 'role':
    $store = 'RoleStore';
    break;
  case 'roletype':
    $store = 'RoleTypeStore';
    break;
  case 'site':
    $store = 'SiteStore';
    break;
  case 'useridrole':
    $store = 'PersonStore';
    break;
  default:
    $store = null;
  }
  return $store;
}






function serializeEntities($entities, $content) {
	global $tm;
	//$tm->tv("Made It To serializeEntities Function");
	switch ($content) {
  case 'text/xml':
    $personnel =& PersonnelDB::getInstance();
    $xml_doc = $personnel->to_xml($entities);
    return $xml_doc->saveXML();
    break;
  case 'application/json':
    break; 
  }
}






function unserializeEntities($xml, $entityType) {
	global $tm;
	//$tm->tv('XML2: '.print_r($xml, true));
  $personnel =& PersonnelDB::getInstance();
  //$tm->tv('PERSONNEL VAR: '.print_r($personnel, true));
  $xml_doc = new DOMDocument();
  $xml_doc->loadXML($xml);

	$xml = simplexml_import_dom($xml_doc);
	//$tm->tv('SERVER 3: '.print_r($xml, true));

  return $personnel->from_xml($xml_doc, $entityType);
}






// ************************************ { 2013-08-16 - RC } ************************************
// THIS FUNCTION BOTH AUTHENTICATES AND AUTHORIZES THE USER FOR MODIFYING THE DATABASE
// NO SPECIAL PERMISSIONS ARE NEEDED FOR READING
// USERS WHO ARE ATTEMPING TO INSERT A NEW RECORD OR UPDATE AN EXISTING ONE WILL NEED TO BE 
// - SIGNED IN AND THEY NEED TO HAVE THE APPROPRIATE PERMISSIONS TO PERFORM THE INSERT/EDIT
// *********************************************************************************************
function authorize($server) {



	// ************************************ { 2013-08-16 - RC } ************************************
	// SET THE DEFAULTS
	// *********************************************************************************************
	global $tm;
	$process_via_ldap = true;






	// ************************************ { 2013-08-16 - RC } ************************************
	// GET THE USER EITHER FROM THE AUTH TOKEN OR THE SESSION VARIABLE (FOR WEBSITE ENTRIES)
	// - IF THEY COME IN THROUGH THE WEBSITE, WE CAN GET THEIR USERNAME FROM THE SESSION, BUT WE 
	// 	 WILL NOT HAVE THEIR PASSWORD.  SINCE THEY HAVE ALREADY AUTHENTICATED THROUGH LDAP,
	//	 WE DO NOT HAVE TO VALIDATE THEM AGAIN
	// *********************************************************************************************
	if (isset($server->auth)) {
		$auth_token = $server->auth;
	}
	else {
		$tm->tv("No Auth Token Was Set And User Is Not Logged In Via The Web Site.");
		return false;
	}






	// ************************************ { 2013-08-16 - RC } ************************************
	// PULL OUT THE SITES
	// *********************************************************************************************
	if (preg_match_all('/<siteAcronym>(.*)<\/siteAcronym>/iU', $server->body, $matches)) {
		$unique_sites = array_unique($matches[1]);
	}
	$tm->tv("UNIQUE SITES: ".print_r($unique_sites, true));






	// ************************************ { 2013-08-09 - RC } ************************************
	// SPLIT THE USERNAME AND PASSWORD APART
	// *********************************************************************************************
	$decoded_auth = base64_decode($auth_token);
	if (preg_match('/:/', $decoded_auth)) {

		list($bind_user, $bind_pass) = explode(':', $decoded_auth);
		if (preg_match('/^person_/', $bind_user)) {
			$bind_user = preg_replace('/^person_/', '', $bind_user);
			$process_via_ldap = false;
		}

	}
	else {
		return false;
	}






	// ************************************ { 2013-08-16 - RC } ************************************
	// START THE DATABASE CONNECTION SO WE CAN CHECK THE PRIVILEGES TABLE FOR AUTHORIZATION
	// *********************************************************************************************
	$personnel =& PersonnelDB::getInstance();
	$store_name = getEntityStore(strtolower('Site'));






	// ************************************ { 2013-08-16 - RC } ************************************
	// CHECK TO SEE IF THE USER HAS PERMISSION TO ACCESS THIS RESOURCE FOR THIS SITE
	// *********************************************************************************************
	foreach ($unique_sites AS $siteAcronym) {



		// ************************************ { 2013-08-16 - RC } ************************************
		// GET THE SITE ID FROM THE SITE ACRONYM
		// *********************************************************************************************
		$site_info = $personnel->$store_name->getByAcronym($siteAcronym);
		$tm->tv("SITE ID NOW: ".$site_info->siteID);
		$site = $site_info->siteID;






		// ************************************ { 2013-08-16 - RC } ************************************
		// TAKE THE USER AND SITE ID AND SEE IF THEY HAVE A PERMISSION LEVEL HIGH ENOUGH TO MODIFY THE DB
		// *********************************************************************************************
		if ($permission = $personnel->$store_name->lookup_permissions($bind_user, $site)) {



			// ************************************ { 2013-08-16 - RC } ************************************
			// PULL OUT THE PERMISSION LEVEL
			// *********************************************************************************************
			$permission_level = $permission->permission;
			$tm->tv("PERMISSION LEVEL: ".$permission->permission);






			// ************************************ { 2013-08-16 - RC } ************************************
			// PERMISSION MUST BE AT LEAST A LEVEL 4
			// *********************************************************************************************
			if ($permission_level & 2) {
				// USER HAS PERMISSION
				$tm->tv('PERMISSIONS WERE SET FOR USER '.$bind_user.' AT SITE '.$site.' AND THEY WERE ADEQUATE');
			}
			else {
				$tm->tv('PERMISSIONS WERE SET FOR USER '.$bind_user.' AT SITE '.$site.', HOWEVER THEY WERE NOT ADEQUATE');
				return false;
			}



		}
		else {
			$tm->tv('PERMISSIONS WERE NOT SET FOR THIS USER '.$bind_user.' & SITE '.$site);
			return false;
		}



	}






	// ************************************ { 2013-08-09 - RC } ************************************
	// IF WE HAVE AN AUTH TOKEN, TRY AND AUTHENTICATE WITH IT
	// *********************************************************************************************
	if (($auth_token) && ($process_via_ldap)) {
		$tm->tv("AUTH TOKEN: ".$auth_token);



		// ************************************ { 2013-08-09 - RC } ************************************
		// SEND THE AUTH TOKEN TO THE LDAP FUNCTION
		// FUNCTION LIVES AT THE BOTTOM OF THIS FILE
		// *********************************************************************************************
		if (ldap_verification($auth_token)) {
			$tm->tv("LDAP VERIFIED");
			return true;
		}
		else {
			$tm->tv("LDAP NOT VERIFIED");
			return false;
		}



	}
	// WEBSITE ENTRY
	elseif ($auth_token) {
		$tm->tv("AUTH TOKEN: ".$auth_token);
		return true;
	}
	else {
		$tm->tv("Error Processing The Authorization Request");
		return false;
	}



}






// ************************************ { 2013-08-22 - RC } ************************************
// ATTEMPTS TO CREATE A NEW LDAP USER
// *********************************************************************************************
function ldap_insert($person_id, $user_info) {



	global $tm;



	// ************************************ { 2013-08-09 - RC } ************************************
	// SET OTHER DEFAULTS (NOT LDAP RELATED)
	// *********************************************************************************************
	$message = array();
	$ldap_query = 'Creating A New User';






	// ************************************ { 2013-06-10 - RC } ************************************
	// TRY AND ESTABLISH A CONNECTION TO THE LDAP SERVER
	// *********************************************************************************************
	$ds = ldap_connect(LDAP_SERVER, 389);
	$ldap_admin_bind = false;






	// ************************************ { 2013-06-10 - RC } ************************************
	// SET LDAP OPTIONS
	// *********************************************************************************************
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);






	// ************************************ { 2013-08-22 - RC } ************************************
	// PULL OUT THE USER DATA
	// *********************************************************************************************
	$xml = new SimpleXMLElement($user_info, LIBXML_NOCDATA);
	
	$first_name = (string)$xml->person->identity->firstName;
	$last_name = (string)$xml->person->identity->lastName;
	$email = (string)$xml->person->identity->primaryEmail;
	$user_id = (string)$xml->person->identity->ldapUser;
	$primary_site = (string)$xml->person->contactInfoList->contactInfo->siteAcronym;
	$title = (string)$xml->person->identity->title;
	$user_password = (string)$xml->person->identity->ldapPass;






	// ************************************ { 2013-08-22 - RC } ************************************
	// PRINT OUT THE USER INFORMATION FOR DEBUGGING PURPOSES
	// *********************************************************************************************
	$tm->tv('first_name: '     .$first_name   );
	$tm->tv('last_name: '      .$last_name    );
	$tm->tv('email: '          .$email        );
	$tm->tv('user_id: '        .$user_id);
	$tm->tv('person_id: '      .$person_id    );
	$tm->tv('primary_site: '   .$primary_site );
	$tm->tv('title: '          .$title        );
	$tm->tv('user_password: '  .$user_password);






	// ************************************ { 2013-08-22 - RC } ************************************
	// TRY AND BIND TO THE ADMIN ACCOUNT
	// FUNCTION LIVES AT THE BOTTOM OF THIS FILE
	// *********************************************************************************************
	if (admin_bind($ds, LDAP_ADMIN_DN, LDAP_ADMIN_PASS, $message)) {
		$message[] = '<FONT COLOR=BLUE>Attempting To Create A New User</FONT>';



		// ************************************ { 2013-08-22 - RC } ************************************
		// ATTEMPT TO CREATE THE USER
		// FUNCTION LIVES AT THE BOTTOM OF THIS FILE
		// *********************************************************************************************
		if ($user_info_array = insert_ldap_user($ds, $first_name, $last_name, $email, $user_id, $person_id, $primary_site, $title, $user_password, LDAP_USER_BASE_DN, $message)) {
			$user_id = $user_info_array[0]['uid'][0];
			$message[] = '<FONT COLOR=GREEN>User \''.$user_id.'\' Successfully Created</FONT>';
			$message[] = 'User Info: <FONT COLOR=ORANGE>'.print_r($user_info_array, true).'</FONT>';
			$message['success'] = true;
		}
		else {
			$message[] = '<FONT COLOR=RED>No Insertion Information Was Returned For User \''.$user_id.'\'</FONT>';
			$message['success'] = false;
		}
	}
	else {
		$message[] = '<FONT COLOR=RED>Error Creating User \''.$user_id.'\': '.ldap_error($ds).'</FONT>';
		$message['success'] = false;
	}






	// ************************************ { 2013-08-22 - RC } ************************************
	// SET THE STATUSES - FOR LOGGING PURPOSES
	// *********************************************************************************************
	if ($message['success']) {
		$ldap_result = 'succeeded';
	}
	else {
		$ldap_result = 'failed';
	}






	// ************************************ { 2013-08-22 - RC } ************************************
	// BUILD OUT THE MESSAGE STRING TO LOG TO THE DATABASE SO WE CAN SEE WHAT HAPPENED
	// *********************************************************************************************
	if (!$message) {
		$message_string = 'No Message Was Returned';
	}
	else {
		$i = 0;
		$message_string = '';
		$message_string_html = '';
		foreach ($message AS $message_key => $message_val) {
			++$i;
			$message_string .= "\n".$i.". [".$message_key."] => ".preg_replace('/&#039;/', '"', htmlentities(strip_tags($message_val), ENT_QUOTES));
			$message_string_html .= "<br>".$i.". [".$message_key."] => ".htmlentities($message_val, ENT_QUOTES);
		}	
	}






	// ************************************ { 2013-08-22 - RC } ************************************
	// LOG THE MESSAGES TO THE DATABASE
	// *********************************************************************************************
	$personnel =& \WSWG\iDBConnection::getInstance(DATABASE);
	$person_results = $personnel->query("
		INSERT INTO 
			lter_personnel.ldap_staging 
		SET 
			id = 0,
			ldap_user = '".$user_id."',
			ldap_action = 'insert',
			ldap_query = '".$ldap_query."',
			ldap_result = '".$ldap_result."',
			ldap_debug_message = '".$message_string."',
			ldap_debug_message_html = '".$message_string_html."',
			date = '".date("Y-m-d H:i:s")."'");
	$tm->tv('PERSON RESULTS: '.print_r($person_results, true));
	
	
	
	$update_user_id_results = $personnel->query("
		UPDATE 
			lter_personnel.person 
		SET 
			userID = '".$user_id."'
		WHERE
			personID = '".$person_id."'");
	$tm->tv('UPDATE USER ID RESULTS: '.print_r($update_user_id_results, true));



	return $message;



}






// ************************************ { 2013-08-22 - RC } ************************************
// ATTEMPTS TO MODIFY AN EXISTING LDAP ENTRY
// *********************************************************************************************
function ldap_update($person_user_id, $user_info) {



	global $tm;



	// ************************************ { 2013-08-09 - RC } ************************************
	// SET OTHER DEFAULTS (NOT LDAP RELATED)
	// *********************************************************************************************
	$message = array();
	$ldap_query_array = array();






	// ************************************ { 2013-06-10 - RC } ************************************
	// TRY AND ESTABLISH A CONNECTION TO THE LDAP SERVER
	// *********************************************************************************************
	$ds = ldap_connect(LDAP_SERVER, 389);
	$ldap_admin_bind = false;






	// ************************************ { 2013-06-10 - RC } ************************************
	// SET LDAP OPTIONS
	// *********************************************************************************************
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);






	// ************************************ { 2013-08-22 - RC } ************************************
	// PULL OUT THE USER DATA
	// *********************************************************************************************
	$xml = new SimpleXMLElement($user_info, LIBXML_NOCDATA);

	$first_name = (string)$xml->person->identity->firstName;
	$last_name = (string)$xml->person->identity->lastName;
	$email = (string)$xml->person->identity->primaryEmail;
	$user_id = $person_user_id;
	$new_user_id = (string)$xml->person->identity->ldapUser;
	$person_id = (string)$xml->person->personID;
	$primary_site = (string)$xml->person->contactInfoList->contactInfo->siteAcronym;
	$title = (string)$xml->person->identity->title;
	$user_password = (string)$xml->person->identity->ldapPass;






	// ************************************ { 2013-08-22 - RC } ************************************
	// PRINT OUT THE USER INFORMATION FOR DEBUGGING PURPOSES
	// *********************************************************************************************
	$tm->tv('first_name: '     .$first_name   );
	$tm->tv('last_name: '      .$last_name    );
	$tm->tv('email: '          .$email        );
	$tm->tv('user_id: '        .$user_id);
	$tm->tv('new_user_id: '    .$new_user_id  );
	$tm->tv('person_id: '      .$person_id    );
	$tm->tv('primary_site: '   .$primary_site );
	$tm->tv('title: '          .$title        );
	$tm->tv('user_password: '  .$user_password);






	// ************************************ { 2013-08-22 - RC } ************************************
	// DETERMINE WHAT KIND OF ACTION WE WILL BE DOING - FOR LOGGING PURPOSES
	// *********************************************************************************************
	if (($new_user_id) && ($new_user_id != $user_id)) {
		$ldap_query_array[] = "Renaming Existing User \"".$user_id."\" To \"".$new_user_id."\"";
	}
	
	if ($user_password) {
		$ldap_query_array[] = "Setting A New Password";
	}

	$ldap_query = implode('; ', $ldap_query_array);






	// ************************************ { 2013-08-22 - RC } ************************************
	// CHECK TO SEE IF WE EVEN NEED TO DO THE UPDATE
	// *********************************************************************************************
	if (($new_user_id) || ($user_password)) {
//	if (($new_user_id)) {



		// ************************************ { 2013-08-22 - RC } ************************************
		// TRY AND BIND TO THE ADMIN ACCOUNT
		// FUNCTION LIVES AT THE BOTTOM OF THIS FILE
		// *********************************************************************************************
		if (admin_bind($ds, LDAP_ADMIN_DN, LDAP_ADMIN_PASS, $message)) {
			$message[] = '<FONT COLOR=BLUE>Attempting To Modify User \''.$user_id.'\'</FONT>';



			// ************************************ { 2013-08-22 - RC } ************************************
			// ATTEMPT TO MODIFY THE USER
			// FUNCTION LIVES AT THE BOTTOM OF THIS FILE
			// *********************************************************************************************
//			if ($user_info_array = modify_ldap_user($ds, $first_name, $last_name, $email, $user_id, $new_user_id, $person_id, $primary_site, $title,  LDAP_USER_BASE_DN, $message)) {
			if ($user_info_array = modify_ldap_user($ds, $first_name, $last_name, $email, $user_id, $new_user_id, $person_id, $primary_site, $title, $user_password, LDAP_USER_BASE_DN, $message)) {
				$message[] = '<FONT COLOR=GREEN>User \''.$user_id.'\' Successfully Modified</FONT>';
				$message[] = 'User Info: <FONT COLOR=ORANGE>'.print_r($user_info_array, true).'</FONT>';
				$message['success'] = true;
			}
			else {
				$message[] = '<FONT COLOR=RED>No Insertion Information Was Returned For User \''.$user_id.'\'</FONT>';
				$message['success'] = false;
			}
		}
		else {
			$message[] = '<FONT COLOR=RED>Error Modifying User \''.$user_id.'\': '.ldap_error($ds).'</FONT>';
			$message['success'] = false;
		}






		// ************************************ { 2013-08-22 - RC } ************************************
		// SET THE STATUSES - FOR LOGGING PURPOSES
		// *********************************************************************************************
		if ($message['success']) {
			$ldap_result = 'succeeded';
		}
		else {
			$ldap_result = 'failed';
		}






		// ************************************ { 2013-08-22 - RC } ************************************
		// BUILD OUT THE MESSAGE STRING TO LOG TO THE DATABASE SO WE CAN SEE WHAT HAPPENED
		// *********************************************************************************************
		if (!$message) {
			$message_string = 'No Message Was Returned';
		}
		else {
			$i = 0;
			$message_string = '';
			$message_string_html = '';
			foreach ($message AS $message_key => $message_val) {
				++$i;
				$message_string .= "\n".$i.". [".$message_key."] => ".preg_replace('/&#039;/', '"', htmlentities(strip_tags($message_val), ENT_QUOTES));
				$message_string_html .= "<br>".$i.". [".$message_key."] => ".htmlentities($message_val, ENT_QUOTES);
			}	
		}






		// ************************************ { 2013-08-22 - RC } ************************************
		// LOG THE MESSAGES TO THE DATABASE
		// *********************************************************************************************
		$personnel =& \WSWG\iDBConnection::getInstance(DATABASE);
		$person_results = $personnel->query("
			INSERT INTO 
				lter_personnel.ldap_staging 
			SET 
				id = 0,
				ldap_user = '".$user_id."',
				ldap_action = 'update',
				ldap_query = '".$ldap_query."',
				ldap_result = '".$ldap_result."',
				ldap_debug_message = '".$message_string."',
				ldap_debug_message_html = '".$message_string_html."',
				date = '".date("Y-m-d H:i:s")."'");
		$tm->tv('PERSON RESULTS: '.print_r($person_results, true));



	}
	// ************************************ { 2013-08-22 - RC } ************************************
	// NO NEW USERNAME OR PASSWORD - LEAVE LDAP ALONE
	// *********************************************************************************************
	else {
		$message[] = 'No LDAP Update Was Needed.';
		$message['success'] = true;
	}



	return $message;



}






function ldap_verification($auth_token) {



	global $tm;



	// ************************************ { 2013-08-09 - RC } ************************************
	// SET OTHER DEFAULTS (NOT LDAP RELATED)
	// *********************************************************************************************
	$message = array();






	// ************************************ { 2013-06-10 - RC } ************************************
	// TRY AND ESTABLISH A CONNECTION TO THE LDAP SERVER
	// *********************************************************************************************
	$ds = ldap_connect(LDAP_SERVER, 389);
	$ldap_admin_bind = false;






	// ************************************ { 2013-06-10 - RC } ************************************
	// SET LDAP OPTIONS
	// *********************************************************************************************
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);






	// ************************************ { 2013-08-09 - RC } ************************************
	// SPLIT THE USERNAME AND PASSWORD APART
	// *********************************************************************************************
	$decoded_auth = base64_decode($auth_token);
	if (preg_match('/:/', $decoded_auth)) {
		list($bind_user, $bind_pass) = explode(':', $decoded_auth);
	}
	else {
		return false;
	}






	// ************************************ { 2013-08-09 - RC } ************************************
	// ATTEMPT TO BIND THE ADMIN USER, DO A LOOKUP FOR THE REGULAR USER AND THEN TRY AND BIND THE REGULAR USER
	// *********************************************************************************************
	if (admin_bind($ds, LDAP_ADMIN_DN, LDAP_ADMIN_PASS, $message)) {
		$message[] = '<FONT COLOR=BLUE>Attempting To Bind User \''.$bind_user.'\'</FONT>';
		if ($user_info_array = bind_user($ds, $bind_user, $bind_pass, LDAP_USER_BASE_DN, $message)) {
			$message[] = '<FONT COLOR=GREEN>Successfully Able To Bind User \''.$bind_user.'\'</FONT>';
			$message[] = 'User Info: <FONT COLOR=ORANGE>'.print_r($user_info_array, true).'</FONT>';
			return true;
		}
		else {
			$message[] = '<FONT COLOR=RED>No Binding Information Was Returned For User \''.$bind_user.'\': '.ldap_error($ds).'</FONT>';
			return false;
		}
	}
	else {
		$message[] = '<FONT COLOR=RED>Error Binding User \''.$bind_user.'\': '.ldap_error($ds).'</FONT>';
		return false;
	}


	$tm->tv("MESSAGES: ".print_r($message, true));
}






// ************************************ { 2013-07-11 - RC } ************************************
// FUNCTION TO BIND THE ADMINISTRATIVE USER TO PERFORM LOOKUPS, INSERTS, UPDATES OR DELETES
// *********************************************************************************************
function admin_bind($ds, $ldap_admin_dn, $ldap_admin_pass, &$message) {



	// ************************************ { 2013-06-10 - RC } ************************************
	// TRY AND BIND THE ADMINISTRATIVE USER
	// *********************************************************************************************
	if (ldap_start_tls($ds)) {

		$message[] = "Starting TLS: Attempting To Bind...";
		$ldap_admin_bind = ldap_bind($ds, $ldap_admin_dn, $ldap_admin_pass);

	}
	else {

		$message[] = "Could Not Start TLS";

	}






	// ************************************ { 2013-07-11 - RC } ************************************
	// CHECK TO SEE IF WE WERE ABLE TO SUCCESSFULLY MAKE THE BIND
	// *********************************************************************************************
	if (!$ldap_admin_bind) {
		$message[] = "<FONT COLOR=RED><B>ERROR: Not Able To Bind Admin To LDAP.</B></FONT>";
		return false;
	}
	else {
		$message[] = "<FONT COLOR=GREEN><B>OK: Was Able To Bind Admin To LDAP Successfully.</B></FONT>";
		return true;
	}



}






// ************************************ { 2013-07-11 - RC } ************************************
// TRY TO BIND A USER TO LDAP WITH A USERNAME AND PASSWORD
// *********************************************************************************************
function bind_user($ds, $bind_user, $bind_pass, $ldap_user_base_dn, &$message) {



	// ************************************ { 2013-07-11 - RC } ************************************
	// BUILD THE USER DN FROM THE BASE DN
	// *********************************************************************************************
	$ldap_user_dn = 'uid='.$bind_user.','.$ldap_user_base_dn;
	$message[] = "LDAP User DN: ".$ldap_user_dn;






	// ************************************ { 2013-06-10 - RC } ************************************
	// DO AN LDAP SEARCH TO SEE IF THE USERNAME EXISTS
	// *********************************************************************************************
	$filter = 'uid='.$bind_user; 
	$justthese = array('uid'); 
	//$sr = ldap_search($ds, $ldap_user_dn, $filter, $justthese);
	$sr = ldap_search($ds, $ldap_user_dn, $filter);
	$info = ldap_get_entries($ds, $sr);

	if ($info["count"] == 0) {

		$message[] = "<FONT COLOR=RED>Did Not Find An LDAP Username Match For ".$bind_user."</FONT>";
		return false;

	}
	else {



		// ************************************ { 2013-06-10 - RC } ************************************
		// ATTEMPT TO BIND TO THE USER WE JUST LOOKED UP WITH THE SUPPLIED PASSWORD
		// *********************************************************************************************
		$ldap_user_dn_from_lookup = $info[0]['dn'];
		$message[] = "LDAP USER DN FROM LOOKUP: ".$ldap_user_dn_from_lookup;
		$ldap_user_bind = ldap_bind($ds, $ldap_user_dn_from_lookup, $bind_pass);

		if (!$ldap_user_bind) {
			$message[] = "<FONT COLOR=RED><B>ERROR: Not Able To Bind User To LDAP.</B></FONT>";
			return false;
		}
		else {
			$message[] = "<FONT COLOR=GREEN><B>OK: Was Able To Bind User To LDAP Successfully.</B></FONT>";
			return $info;
		}



	}



}






// ************************************ { 2013-07-11 - RC } ************************************
// LOOK UP THE USER'S INFORMATION STORED WITHIN LDAP
// *********************************************************************************************
function user_info($ds, $lookup_user, $ldap_user_base_dn, &$message) {



	// ************************************ { 2013-07-11 - RC } ************************************
	// BUILD THE USER DN FROM THE BASE DN
	// *********************************************************************************************
	$ldap_user_dn = 'uid='.$lookup_user.','.$ldap_user_base_dn;
	$message[] = "LDAP User DN: ".$ldap_user_dn;






	// ************************************ { 2013-06-10 - RC } ************************************
	// DO AN LDAP SEARCH TO SEE IF THE USERNAME EXISTS
	// *********************************************************************************************
	$filter = 'uid='.$lookup_user; 
	$justthese = array('uid'); 
	//$sr = ldap_search($ds, $ldap_user_dn, $filter, $justthese);
	$sr = ldap_search($ds, $ldap_user_dn, $filter);
	$info = ldap_get_entries($ds, $sr);

	if ($info["count"] == 0) {
		$message[] = "Did Not Find An LDAP Username Match";
		return false;
	}
	else {
		return $info;
	}



}






// ************************************ { 2013-07-11 - RC } ************************************
// CREATE A NEW LDAP USER
// *********************************************************************************************
function insert_ldap_user($ds, $first_name, $last_name, $email, $user_id, $person_id, $primary_site, $title, $user_password, $ldap_user_base_dn, &$message) {



	// ************************************ { 2013-08-23 - RC } ************************************
	// SET SOME DEFAULTS
	// *********************************************************************************************
	$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789"; // FOR PASSWORD GENERATION
	$pass = array();
	$does_username_exist = true;  // SAY THE USERNAME EXISTS SO WE CAN RUN THROUGH THE DUPE CHECK AT LEAST ONCE
	$j = 0; // COUNTER






	// ************************************ { 2013-08-23 - RC } ************************************
	// GENERATE A PASSWORD
	// *********************************************************************************************
	$alphaLength = strlen($alphabet) - 1; // GET THE LENGTH OF THE ALPHABET STRING

	for ($i = 0; $i < 8; $i++) { // LOOP UP TO 8 CHARACTERS
		$n = rand(0, $alphaLength); // GRAB A RANDOM CHARACTER FROM THE ALPHABET STRING
		$pass[] = $alphabet[$n]; // STORE IT 
	}

//	$user_password = implode($pass);






	// ************************************ { 2013-08-23 - RC } ************************************
	// CREATE THE LDAP USERNAME - COMBINE FIRST INITIAL WITH LAST NAME
	// *********************************************************************************************
	$suggested_username = strtolower(substr(preg_replace('/[^A-Za-z]/', '', $first_name), 0, 1).preg_replace('/[^A-Za-z]/', '', $last_name));
	$user_id = $suggested_username;






	// ************************************ { 2013-08-23 - RC } ************************************
	// KEEP CIRCLING THROUGH THIS LOOP UNTIL '$does_username_exist' IS FALSE
	// *********************************************************************************************
	while ($does_username_exist) {



		// ************************************ { 2013-07-11 - RC } ************************************
		// BUILD THE USER DN FROM THE BASE DN
		// *********************************************************************************************
		$ldap_user_dn = 'uid='.$user_id.','.$ldap_user_base_dn;






		// ************************************ { 2013-08-23 - RC } ************************************
		// DO A USERNAME SEARCH TO SEE IF THAT USERNAME ALREADY EXISTS
		// *********************************************************************************************
		$filter = 'uid='.$user_id; 
		$justthese = array('uid'); 
	
		if ($sr = @ldap_search($ds, $ldap_user_dn, $filter)) {
			$info = ldap_get_entries($ds, $sr);
		}
		else {
			$info["count"] = 0;
		}

		// USERNAME DOES NOT EXIST
		if ($info["count"] == 0) {

			$does_username_exist = false;

		}
		//USERNAME ALREADY EXISTS - TRY AGAIN
		else { 

			// ADD A NUMBER TO THE END OF THE USERNAME AND LOOP THROUGH AGAIN
			$user_id = $suggested_username.++$j;
			$does_username_exist = true;

		}



	}






	// ************************************ { 2013-08-23 - RC } ************************************
	// ADD THE USER ID TO LDAP
	// SET UP THE FIELDS NECESSARY TO CREATE NEW LDAP ENTRY
	// *********************************************************************************************
	$message[] = "LDAP User DN: ".$ldap_user_dn;
	$message[] = 'Generated Password Is: '.$user_password;
	$message[] = 'Attempting To Create New LDAP Entry For User ID: \''.$user_id.'\'';

	$entry["o"] = "LTER";

	$entry["objectClass"][0] = "top";
	$entry["objectClass"][1] = "person";
	$entry["objectClass"][2] = "organizationalPerson";
	$entry["objectClass"][3] = "inetOrgPerson";
	$entry["objectClass"][4] = "uidObject";

	$entry["cn"] = $first_name." ".$last_name;
	$entry["sn"] = $last_name;
	$entry["givenname"] = $first_name;

	$entry["mail"] = $email;
	$entry["uid"] = $user_id;
	$entry["employeeNumber"] = $person_id;
	$entry["ou"] = $primary_site;
	$entry["title"] = $title;
	$entry["userPassword"] = "{SHA}" . base64_encode( pack( "H*", sha1( $user_password ) ) );

	$message[] = "<FONT COLOR=BLUE>User Details: ".print_r($entry, true)."</FONT>";



	// ************************************ { 2013-07-11 - RC } ************************************
	// PERFORM THE INSERT
	// *********************************************************************************************
	if (ldap_add($ds, $ldap_user_dn, $entry)) {
		$message[] = "<FONT COLOR=GREEN><B>User '".$user_id."' Added Successfully!</B></FONT>";



			// ************************************ { 2013-06-10 - RC } ************************************
			// DO AN LDAP SEARCH TO SEE IF THE USERNAME EXISTS
			// *********************************************************************************************
			$filter = 'uid='.$user_id; 
			$justthese = array('uid'); 
			//$sr = ldap_search($ds, $ldap_user_dn, $filter, $justthese);
			$sr = ldap_search($ds, $ldap_user_dn, $filter);
			$info = ldap_get_entries($ds, $sr);


			if ($info["count"] == 0) {
				$message[] = "<FONT COLOR=RED>Did Not Find An LDAP Username Match.  The Record Was Not Created: ".ldap_error($ds)."</FONT>";
				return false;
			}
			else {
				return $info;
			}



	}
	else {
		$message[] = "<FONT COLOR=RED>Error - User ".$user_id." Could Not Be Added: ".ldap_error($ds)."</FONT>";
		return false;
	}



}






// ************************************ { 2013-07-12 - RC } ************************************
// MODIFY AN EXISTING LDAP USER
// *********************************************************************************************
//function modify_ldap_user($ds, $first_name, $last_name, $email, $user_id, $new_user_id, $person_id, $primary_site, $title, $ldap_user_base_dn, &$message) {
function modify_ldap_user($ds, $first_name, $last_name, $email, $user_id, $new_user_id, $person_id, $primary_site, $title, $user_password, $ldap_user_base_dn, &$message) {



	// ************************************ { 2013-07-11 - RC } ************************************
	// BUILD THE USER DN FROM THE BASE DN
	// *********************************************************************************************
	$ldap_user_original_dn = 'uid='.$user_id.','.$ldap_user_base_dn;
	$ldap_user_dn = 'uid='.$new_user_id.','.$ldap_user_base_dn;
	$message[] = "LDAP User DN: ".$ldap_user_dn;






	// ************************************ { 2013-06-10 - RC } ************************************
	// DO AN LDAP SEARCH TO SEE IF THE USERNAME EXISTS
	// *********************************************************************************************
	$filter = 'uid='.$user_id; 
	$justthese = array('uid'); 
	//$sr = ldap_search($ds, $ldap_user_dn, $filter, $justthese);
	$sr = ldap_search($ds, $ldap_user_original_dn, $filter);
	$info = ldap_get_entries($ds, $sr);

	if ($info["count"] == 0) {
		$message[] = 'Could Not Find A User With ID \''.$user_id.'\' To Perform The Modification On.';
		return false;
	}
	else {



		// ************************************ { 2013-07-12 - RC } ************************************
		// PULL OUT THE INFORMATION FROM THE EXISTING LDAP RECORD SO WE CAN COMPARE IT 
		//	- AGAINST WHAT THEY ARE SENDING IN NOW
		// *********************************************************************************************
		$ldap_from_lookup_dn             = $info[0]['dn'];
		$ldap_from_lookup_cn             = $info[0]['cn'][0];
		$ldap_from_lookup_sn             = $info[0]['sn'][0];
		$ldap_from_lookup_givenname      = $info[0]['givenname'][0];
		$ldap_from_lookup_mail           = $info[0]['mail'][0];
		$ldap_from_lookup_uid            = $info[0]['uid'][0];
		$ldap_from_lookup_employeenumber = $info[0]['employeenumber'][0];
		$ldap_from_lookup_ou             = $info[0]['ou'][0];
		$ldap_from_lookup_title          = $info[0]['title'][0];
//		$ldap_from_lookup_userpassword   = $info[0]['userpassword'][0];






		// ************************************ { 2013-07-12 - RC } ************************************
		// COMPARE THE OLD ENTRY WITH THE NEW ENTRY
		// *********************************************************************************************

		// DN
		if ($ldap_user_dn != $ldap_from_lookup_dn) { // DNs DO NOT MATCH - WE ARE GOING TO ATTEMPT TO RENAME THIS USER


			$message[] = 'The DNs Do Not Match.  Attempt To Rename The User';
			// ************************************ { 2013-06-10 - RC } ************************************
			// CHECK TO SEE IF THE NEW USER ID ALREADY EXISTS SO WE DON'T CREATE ONE WITH THE SAME NAME
			// *********************************************************************************************
			$filter = 'uid='.$new_user_id; 
			$justthese = array('uid'); 
			//$sr = ldap_search($ds, $ldap_user_dn, $filter, $justthese);
			$sr = ldap_search($ds, $ldap_user_dn, $filter);
			$info = ldap_get_entries($ds, $sr);

			if ($info["count"] == 0) {
				$message[] = '<FONT COLOR=GREEN>The User ID \''.$new_user_id.'\' Does Not Exists In The LDAP Tree.  We Can Go Ahead And Rename It.</FONT>';
				$rename_entry = true;
			}
			else {
				$message[] = '<FONT COLOR=RED>The User ID \''.$new_user_id.'\' Already Exists In The LDAP Tree.  Please Choose A Different ID.</FONT>';
				$rename_entry = false;
			}



		}
		else {
			$rename_entry = false;
		}


		// CN
		$ldap_compare_cn = $first_name." ".$last_name;
		if ($ldap_compare_cn == $ldap_from_lookup_cn) {
			$cn = $ldap_from_lookup_cn;
			$message[] = 'CN Is The Same - Do Not Update';
		}
		else {
			$cn = $ldap_compare_cn;
			$message[] = 'CN Is Different - Update';
		}


		// SN
		if ($last_name == $ldap_from_lookup_sn) {
			$sn = $ldap_from_lookup_sn;
			$message[] = 'SN Is The Same - Do Not Update';
		}
		else {
			$sn = $last_name;
			$message[] = 'SN Is Different - Update';
		}


		// GIVENNAME
		if ($first_name == $ldap_from_lookup_givenname) {
			$givenname = $ldap_from_lookup_givenname;
			$message[] = 'Given Name Is The Same - Do Not Update';
		}
		else {
			$givenname = $first_name;
			$message[] = 'Given Name Is Different - Update';
		}


		// MAIL
		if ($email == $ldap_from_lookup_mail) {
			$mail = $ldap_from_lookup_mail;
			$message[] = 'Email Is The Same - Do Not Update';
		}
		else {
			$mail = $email;
			$message[] = 'Email Is Different - Update';
		}


		// UID
		if ($new_user_id == $ldap_from_lookup_uid) {
			$uid = $ldap_from_lookup_uid;
			$message[] = 'User ID Is The Same - Do Not Update';
		}
		else {
			$uid = $user_id;
			$message[] = 'User ID Is Different - Update';
			$message[] = 'New User ID Is '.$new_user_id;
		}


		// EMPLOYEENUMBER
		if ($person_id == $ldap_from_lookup_employeenumber) {
			$employeeNumber = $ldap_from_lookup_employeenumber;
			$message[] = 'Employee Number Is The Same - Do Not Update';
		}
		else {
			$employeeNumber = $person_id;
			$message[] = 'Employee Number Is Different - Update';
		}

		if ($primary_site == $ldap_from_lookup_ou) {
			$ou = $ldap_from_lookup_ou;
			$message[] = 'OU Is The Same - Do Not Update';
		}
		else {
			$ou = $primary_site;
			$message[] = 'OU Is Different - Update';
		}


		// TITLE
		if ($title == $ldap_from_lookup_title) {
			$title = $ldap_from_lookup_title;
			$message[] = 'Title Is The Same - Do Not Update';
		}
		else {
			$title = $title;
			$message[] = 'Title Is Different - Update';
		}


		// PASSWORD - HASH THE PASSWORD AND COMPARE THE HASH
	//	$user_password = "{SHA}" . base64_encode( pack( "H*", sha1( $user_password ) ) );
//		$ldap_compare_userpassword = "{SHA}" . base64_encode( pack( "H*", sha1( $user_password ) ) );
//		if ($ldap_compare_userpassword == $ldap_from_lookup_userpassword) {
//			$user_password = $ldap_from_lookup_userpassword;
//			$message[] = 'Passwords Are The Same - Do Not Update';
//		}
//		else {
//			$message[] = 'New Password Is: '.$user_password;
//			$user_password = $ldap_compare_userpassword;
//			$message[] = 'Passwords Are Different - Update';
//		}
if ($user_password != ""){
$user_password = "{SHA}" . base64_encode( pack( "H*", sha1( $user_password ) ) );
$message[] = 'New Password Is: '.$user_password; 
$message[] = 'There is a new password - Update';
}


		// ************************************ { 2013-07-10 - RC } ************************************
		// CREATE NEW LDAP ENTRY
		// *********************************************************************************************
		$message[] = "Attempting To Modify LDAP Entry For User ID: ".$user_id;

		$entry["o"] = "LTER";

		$entry["objectClass"][0] = "top";
		$entry["objectClass"][1] = "person";
		$entry["objectClass"][2] = "organizationalPerson";
		$entry["objectClass"][3] = "inetOrgPerson";
		$entry["objectClass"][4] = "uidObject";

		$entry["cn"] = $cn;
		$entry["sn"] = $sn;
		$entry["givenname"] = $givenname;

		$entry["mail"] = $mail;
		$entry["uid"] = $uid;
		$entry["employeeNumber"] = $employeeNumber;
		$entry["ou"] = $ou;
		$entry["title"] = $title;
if ($user_password != ""){		$entry["userPassword"] = $user_password;}

		$message[] = "<FONT COLOR=BLUE>User Details: ".print_r($entry, true)."</FONT>";

		if (ldap_modify($ds, $ldap_user_original_dn, $entry)) {

			$message[] = "<FONT COLOR=GREEN><B>Details For User '".$user_id."' Have Been Modified Successfully!</B></FONT>";



			// ************************************ { 2013-07-12 - RC } ************************************
			// PERFORM THE RENAME (IF NECESSARY)
			// *********************************************************************************************
			if ($rename_entry) {
				$message[] = 'Attempt To Rename The User';
				if (ldap_rename($ds, $ldap_from_lookup_dn, 'uid='.$new_user_id, NULL, true)) {
					$message[] = '<FONT COLOR=GREEN>LDAP User Was Successfully Renamed From \'<FONT COLOR=BLUE>'.$user_id.'</FONT>\' To \'<FONT COLOR=BLUE>'.$new_user_id.'</FONT>\' </FONT>';
				}
				else {
					$message[] = '<FONT COLOR=RED>Error: Cannot Rename User ID: '.ldap_error($ds).'</FONT>';
					return false;
				}
			}
			else {
				$message[] = 'Renaming User Is Not Necessary';
			}



		}
		else {
			$message[] = "<FONT COLOR=RED>Error - Details For User '".$user_id."' Could Not Be Modified: ".ldap_error($ds)."</FONT>";
			return false;
		}



	}



	return true;



}






// ************************************ { 2013-07-11 - RC } ************************************
// DELETE AN LDAP USER
// *********************************************************************************************
function delete_user($ds, $user_id, $ldap_user_base_dn, &$message) {



	// ************************************ { 2013-07-11 - RC } ************************************
	// BUILD THE USER DN FROM THE BASE DN
	// *********************************************************************************************
	$ldap_user_dn = 'uid='.$user_id.','.$ldap_user_base_dn;
	$message[] = "LDAP User DN: ".$ldap_user_dn;






	// ************************************ { 2013-07-10 - RC } ************************************
	// DELETE AN LDAP ENTRY
	// *********************************************************************************************
	$message[] = "Attempting To Delete LDAP Entry For User ID '".$user_id."'";

	if (ldap_delete($ds, $ldap_user_dn)) {

		$message[] = "<FONT COLOR=GREEN><B>User '".$user_id."' Deleted Successfully!</B></FONT>";



		// ************************************ { 2013-06-10 - RC } ************************************
		// DO AN LDAP SEARCH TO SEE IF THE USERNAME EXISTS
		// *********************************************************************************************
		$filter = 'uid='.$user_id; 
		$justthese = array('uid'); 
		//$sr = ldap_search($ds, $ldap_user_dn, $filter, $justthese);
		$sr = ldap_search($ds, $ldap_user_dn, $filter);
		$info = ldap_get_entries($ds, $sr);

		if ($info["count"] == 0) {
			$message[] = "<FONT COLOR=GREEN>Did Not Find An LDAP Username Match When Searching - User Was Deleted!</FONT>";
			return true;
		}
		else {
			$message[] = "<FONT COLOR=RED>Found An LDAP Username Match When Searching - User Was Not Deleted!</FONT>";
			return false;
		}



	}
	else {
		$message[] = "<FONT COLOR=RED>Error - User '".$user_id."' Could Not Be Deleted: ".ldap_error($ds)."</FONT>";
		return false;
	}




}
