<?php

ini_set('display_errors', 1);

include 'PersonnelDBConfig.php';

if (isset($_GET['md'])) {
  switch ($_GET['md']) {
  case 'a':
    $filters = gIf('ln') == 1 ? array('lastName' => $_GET['name']) : array('name' => $_GET['name']);
    $filters['siteAcronym'] = gIf('site');
    $filters['roleType'] = gIf('role');
    
    $params = gIf('ia') == 1 ? array('showInactive' => true) : array();
    break;
    
  case 'b':
    $filters = array('name' => $_GET['q'], 'isActive' => '1');
    $params = array();
    break;
  }
  
  $results = getTransformed('person', 'personTable.xsl', null, $filters, $params);
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>LTER PersonnelDB</title>
    <link rel="stylesheet" href="http://yui.yahooapis.com/2.8.0r4/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css"/>
    <link rel="stylesheet" href="template/css/main.css" type="text/css"/>
    <link rel="stylesheet" href="template/css/yui-reskin.css" type="text/css"/>
  </head>
  <body>
    <div id="doc" class="yui-t7">
      <div id="hd" role="banner">
	<?php include 'template/header.php'; ?>
      </div>
      <div id="bd" role="main">
	<div role="search" class="yui-g">
	  <?php if (isset($_GET['sp']) && $_GET['sp'] == 'a') { ?>
	  <div>
	    <form id="adv-search" action="/personnelDB" method="GET">
	      <input type="hidden" name="sp" value="a"/>
	      <table>
		<tr><th>Name</th><td><input type="text" class="search-input" name="name"/></td></tr>
		<tr><th>&nbsp;</th><td><input type="checkbox" name="ln" value="1"/> search last name only</td></tr>
		<tr><th>Site</th><td><?php echo getTransformed('site', 'selects.xsl'); ?></td></tr>
		<tr><th>Role</th><td><?php echo getTransformed('roleType', 'selects.xsl'); ?></td></tr>
		<tr><th>&nbsp;</th><td><input type="checkbox" name="ia" value="1"/> include inactive roles</td></tr>
	      </table>
	      <button type="submit" name="md" value="a">Search</button>
	    </form>
	  </div>
	  <div>
	    <a href="/personnelDB?sp=b">basic search</a>
	  </div>
	  <?php } else { ?>
	  <div>
	    <form id="basic-search" action="/personnelDB" method="GET">
	      <input type="hidden" name="sp" value="b"/>
	      <input type="text" class="search-input" name="q"/>
	      <?php echo getTransformed('site', 'selects.xsl', null, null, array('selected' => gIf('site'))); ?>
	      <button type="submit" name="md" value="b">Search</button>
	    </form>
	  </div>
	  <div>
	    <a href="/personnelDB?sp=a">advanced search</a>
	  </div>
	  <?php } ?>
	</div>
	<div id="results" role="application" class="yui-g">
	  <?php if(isset($results)) echo $results; ?>
	</div>

      </div>
      <div id="ft" role="contentinfo">
	<?php include 'template/footer.php'; ?>
      </div>
    </div>
  </body>
</html>

<?php

function gIf($index) {
  return isset($_GET[$index]) ? $_GET[$index] : null;
}	     

function eIf($index) {
  echo isset($_GET[$index]) ? $_GET[$index] : '';
}	     

?>