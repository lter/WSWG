<?php

namespace WSWG;

// Configuration
define('LA_SERVER', 'ldap.lternet.edu');
define('LA_VERSION', 3);
define('LA_DOMAIN', 'o=LTER,dc=ecoinformatics,dc=org');
define('LA_SRBINDNAME', null);
define('LA_SRBINDPWD', null);

define('UNAME_FIELD', 'uid');
define('GNAME_FIELD', null);
define('MEMBER_FIELD', null);


// LDAPAuth Class
class LDAPAuth {

  // Member Data
  var $lc;			// LDAP connection variable
  var $domain;			// Base search domain

  // Constructor
  //	Takes LDAP host, version, bind name, and bind password.  If arguments are not supplied,
  //	globals defined in the Configuration section are used.
  function LDAPAuth($s = LA_SERVER, $v = LA_VERSION, $d = LA_DOMAIN, $u = LA_SRBINDNAME, $p = LA_SRBINDPWD) {
    // Connect
    $this->lc = ldap_connect($s) 
      or throw new LDAPAuthException("Could not connect to $s");

    ldap_set_option($this->lc, \LDAP_OPT_PROTOCOL_VERSION, $v)
      or throw new LDAPAuthException("Could not set LDAP verion", $this->lc);

    ldap_set_option($this->lc, \LDAP_OPT_SIZELIMIT, 0)
      or throw new LDAPAuthException("Could not set maximum return size", $this->lc);

    $this->domain = $d;
    
    // Bind for searches, if neccessary
    if (isset($u)) {
      // NYI
    }
  }

  // authenticate
  //	Attempts to bind to the LDAP server using username $u and password $p; returns true on
  //	success and false on failure.
  function authenticate($u, $p) {
    $user = $this->getUserByName($u);

    if ($bind = ldap_bind($this->lc, $user['dn'], $p)) {
      return true;
    } else {
      if (ldap_errno($this->lc) == 49) {
	return false;
      } else {
	throw new LDAPAuthException("LDAP bind for authentication failed", $this->lc);
      }
    }
  }
	
  // getUserByName
  //	Returns an array of information for user specified by $u, or false if no such user exists.
  //	If multiple users exist, generates a warning and returns the first matching user.
  function getUserByName($u) {
    if ($sr = ldap_search($this->lc, $this->domain, UNAME_FIELD."=$u")) {
      $info = ldap_get_entries($this->lc, $sr);

      if ($info["count"] == 0) {
	return false;
      } else {
	if ($info["count"] > 1) {
	  // Generate warning
	  // NYI
	}
	return $info[0];
      }
    } else {
      throw new LDAPAuthException("LDAP search for user failed", $this->lc);
    }
  }
	
  // getUsersByGroup
  //	Returns an array of usernames in group specified by $g, or false if no such group exists.
  function getUsersByGroup($g) {
    if ($sr = ldap_search($this->lc, $this->domain, GNAME_FIELD."=$g", array(MEMBER_FIELD))) {
      $info = ldap_get_entries($this->lc, $sr);

      if ($info["count"] == 0) {
	return false;
      } else {
	if ($info["count"] > 1) {
	  // Generate warning
	}
	return $info[0][MEMBER_FIELD];
      }
    } else {
      throw new LDAPAuthException("LDAP search for users in group failed", $this->lc);
    }	
  }

	
  // getGroupByName
  //	Returns an array of information for group specifiec by $g, or false if no such group exists.
  //	If multiple groups exist, generates a warning and returns the first matching group.
  function getGroupByName($g) {
    if ($sr = ldap_search($this->lc, $this->domain, GNAME_FIELD."=$g")) {
      $info = ldap_get_entries($this->lc, $sr);

      if ($info["count"] == 0) {
	return false;
      } else {
	if ($info["count"] > 1) {
	  // Generate warning
	}
	return $info[0];
      }
    } else {
      throw new LDAPAuthException("LDAP search for group failed", $this->lc);
    }	
  }


  // getGroupsForUser
  //  Returns an array of groups containing user $u, or false if no such group exists.
  function getGroupsForUser($u) {
    if ($sr = ldap_search($this->lc, $this->domain, MEMBER_FIELD."=$u")) {
      $info = ldap_get_entries($this->lc, $sr);

      if ($info["count"] == 0) {
	return false;
      } else {
	foreach ($info as $group) {
	  if (empty($group['cn'][0])) continue;
	  $groups[] = $group['cn'][0];
	}
				
	return $groups;
      }
    } else {
      throw new LDAPAuthException("LDAP search for groups for user failed", $this->lc);
    }		

  }
	
  // userInGroup
  //	Returns true if user $u is in group $g, otherwise false.
  function userInGroup($u, $g) {
    $users = $this->getUsersByGroup($g);
    return in_array($u, $users);	
  }
}



// Exception class for LDAPAuth errors
class LDAPAuthException extends \Exception {
  
  private $lc;

  public function __construct($message, $lc = null) {
    parent::__construct($message);
    $this->lc = $lc;
  }

  public function __toString() {
    if (!isnull($this->lc)) {
      return "'{$this->message}: ".ldap_errno($this->lc)."' on line {$this->line} in {$this->file}";
    } else {
      return "{$this->message} on line {$this->line} in {$this->file}";
    }
  }
}