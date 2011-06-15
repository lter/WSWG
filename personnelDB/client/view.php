<?php

ini_set('display_errors', 1);

include 'PersonnelDBConfig.php';

$person = getTransformed('person', 'person.xsl', $_GET['pid']);

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
	   <?php if (isset($person)) echo $person; ?>
	 </div>
       </div>
       <div id="ft" role="contentinfo">
	 <?php include ROOT_PATH.'template/footer.php'; ?>
       </div>
     </div>
   </body>
 </html>
