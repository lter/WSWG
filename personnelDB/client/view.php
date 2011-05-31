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
     <link rel="stylesheet" href="template/css/main.css" type="text/css"/>
     <link rel="stylesheet" href="template/css/yui-reskin.css" type="text/css"/>
   </head>
   <body>
     <div id="doc" class="yui-t7">
       <div id="hd" role="banner">
	<?php include 'template/header.php'; ?>
       </div>
       <div id="bd" role="main">
	 <div class="yui-g">
	   <?php if (isset($person)) echo $person; ?>
	</div>
      </div>
      <div id="ft" role="contentinfo">
	<?php include 'template/footer.php'; ?>
      </div>
    </div>
  </body>
</html>
