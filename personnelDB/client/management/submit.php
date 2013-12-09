<?php

include '../PersonnelDBConfig.php';

// Create document and root level element
$doc = new DOMDocument();
$personnel = addNode($doc, 'personnel');

// Create a single person instance
$person = addNode($personnel, 'person');
addIf($person, 'personID');


/* IDENTITY */

// Create identity element
$identity = addNode($person, 'identity');

// Populate identity
addRequired($identity, 'ldapUser');
addRequired($identity, 'ldapPass');
addRequired($identity, 'firstName');
addRequired($identity, 'lastName');
addRequired($identity, 'primaryEmail');
addRequired($identity, 'optOut', null, 0);

addMany($identity, array('prefix', 'middleName', 'suffix', 'preferredName', 'title'));

addLines($identity, 'nameAlias', 'nameAlias');


/* ROLES */
// Create role list
$roles = addNode($person, 'roleList');

// Add roles
for($i=1; $i<=$_POST['roleCount']; $i++) {
  $role = addNode($roles, 'role');

  addRequired($role, "roleIsActive_$i", 'isActive', 0);
  addRequired($role, "roleID_$i", 'roleID');
  addRequired($role, "roleSiteAcronym_$i", 'siteAcronym');

  $roleType = addRequired($role, "roleType_$i", 'roleType');
  $roleType->setAttribute('type', $_POST["type_$i"]);

  addMany($role, array('beginDate', 'endDate'), "_$i");
}


/* CONTACT INFO */
// Create contact info list
$contacts = addNode($person, 'contactInfoList');

// Add contact info blocks
for($i=1; $i<=$_POST['contactCount']; $i++) {
  $contact = addNode($contacts, 'contactInfo');

  addRequired($contact, "contactInfoID_$i", 'contactInfoID');
  addRequired($contact, "contactIsActive_$i", 'isActive', 0);
  addRequired($contact, "contactIsPrimary_$i", 'isPrimary', 0);
  addRequired($contact, "contactSiteAcronym_$i", 'siteAcronym');
  addRequired($contact, "label_$i", 'label');

  addMany($contact, array('beginDate', 'endDate', 'institution', 'city', 'administrativeArea', 'postalCode'), "_$i");

  addLines($contact, "address_$i", 'address');
  addLines($contact, "email_$i", 'email');
  addLines($contact, "phone_$i", 'phone');
  addLines($contact, "fax_$i", 'fax');
}

// Choose HTTP method
if (array_key_exists('personID', $_POST) && !empty($_POST['personID'])) {


    // $usr = 'amonkey';
    // $pwd = 'changeme';

    $usr = '';
    $pwd = '';

    if (isset($_SESSION['PDB']['ACCESS'])) {
        $usr = 'person_'.$_SESSION['PDB']['USERNAME'];
        $pwd = 'general_password';
    }



	$auth_token = base64_encode($usr.':'.$pwd);

	// Existing person, use PUT
	$httpOptions = array('method' => 'PUT',
				'header' => array(
					'Accept: text/xml',
					'Content-type: text/xml',
					'Authorization: Basic '. $auth_token
				),
				'timeout' => '10',
				'content' => $doc->saveXML());

//	trigger_error("HTTP OPTIONS: ".print_r($httpOptions, true));

  $httpContext = stream_context_create(array('http' => $httpOptions));
  $url = WS_URL.'person/'.$_POST['personID'];
} else {
  // New person, use POST
  
	// $usr = 'amonkey';
	// $pwd = 'changeme';

	$usr = '';
	$pwd = '';

	if (isset($_SESSION['PDB']['ACCESS'])) {
		$usr = 'person_'.$_SESSION['PDB']['USERNAME'];
		$pwd = 'general_password';
	}


	$auth_token = base64_encode($usr.':'.$pwd);



	$httpOptions = array('method' => 'POST',
				'header' => array(
					'Accept: text/xml',
					'Content-type: text/xml',
					'Authorization: Basic '. $auth_token
				),
				'timeout' => '10',
				'content' => $doc->saveXML());


  $httpContext = stream_context_create(array('http' => $httpOptions));
  $url = WS_URL.'person/';
}

$filec = trim(file_get_contents($url, false, $httpContext));
print($filec);
$xml = new DOMDocument();

$xml->loadXML(trim($filec));
//$xml->loadXML(trim($filec));

$html = processXSLT($xml, XSL_PATH.'person.xsl');
$person = $html->saveHTML();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>LTER PersonnelDB</title>
    <link rel="stylesheet" href="http://yui.yahooapis.com/2.8.0r4/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo PDB_URL; ?>template/css/main.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo PDB_URL; ?>template/css/yui-reskin.css" type="text/css"/>
  </head>
  <body>
    <div id="doc" class="yui-t7">
      <div id="hd" role="banner">
	<?php include ROOT_PATH.'template/header.php'; ?>
      </div>
      <div id="bd" role="main">
	<div class="yui-g">
	  <?php if (isset($person)) echo $person; ?>
	</div>
      </div>
      <div id="ft" role="contentinfo">
	<?php include ROOT_PATH.'template/footer.php'; ?>
      </div>
    </div>
  </body>
</html>

<?php

  /* FUNCTIONS */

  function addNode($node, $tag, $value = '') {
    global $doc;
    
    return $node->appendChild($doc->createElement($tag, $value));
  }

function addIf($node, $key, $tag = null) {
  global $doc;

  if (array_key_exists($key, $_POST) && !empty($_POST[$key])) {
	  $tag = is_null($tag) ? $key : $tag;


	if ($_POST[$key] == 'beginDate') {
		
	}


    return addNode($node, $tag, $_POST[$key]);
  } else {
    return false;
  }
}

function addRequired($node, $key, $tag = null, $default = '') {
  global $doc, $errors;

  $tag = is_null($tag) ? $key : $tag;
  $value = array_key_exists($key, $_POST) ? $_POST[$key] : $default;

  return addNode($node, $tag, $value);
}

function addMany($node, $keys, $suffix = '') {
  foreach ($keys as $key) {
    addIf($node, $key.$suffix, $key);
  }
}

function addLines($node, $key, $tag) {
  if (array_key_exists($key, $_POST)) {
    $lines = explode("\n", $_POST[$key]);
    foreach ($lines as $line) {
      if (empty($line)) continue;
      addNode($node, $tag, trim($line));
    }
  }
}

?>
