<?php

include '../PersonnelDBConfig.php';

// Get person, sites, and roles from web service
if (array_key_exists('pid', $_GET)) {
  $person = getResults('person', $_GET['pid']);
} else {
  $person = getEmpty('person');
}

$sites = getResults('site');
$roles = getResults('roleType');

// Compile into a single XML document
$formXML = new DOMDocument();
$edit = $formXML->appendChild($formXML->createElement('edit'));

$edit->appendChild($formXML->importNode($person->documentElement, true));
$edit->appendChild($formXML->importNode($sites->documentElement, true));
$edit->appendChild($formXML->importNode($roles->documentElement, true));

// Apply XSLT
$form = processXSLT($formXML, XSL_PATH.'personEdit.xsl');
$form = $form->saveHTML();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>LTER PersonnelDB</title>

    <link rel="stylesheet" href="http://yui.yahooapis.com/2.8.0r4/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo PDB_URL; ?>template/css/main.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo PDB_URL; ?>template/css/yui-reskin.css" type="text/css"/>

    <script type="text/javascript" src="<?php echo PDB_URL; ?>js/personneldb.js"></script>
  </head>
  <body>
    <div id="doc" class="yui-t7">
      <div id="hd" role="banner">
	<?php include ROOT_PATH.'template/header.php'; ?>
      </div>
      <div id="bd" role="main">
	<div class="yui-g"> 
	  <?php echo $form; ?>
	</div> 
      </div>
      <div id="ft" role="contentinfo">
	<?php include ROOT_PATH.'template/footer.php'; ?>
      </div>
    </div>
  </body>
</html>