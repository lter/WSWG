<?php

// ws-functions.php
//  Functions to interact with the PersonnelDB web services

// Gets one or more entities as XML from the PersonnelDB webservice
//  $entity: type of entity to be retrieved (person, role, etc.)
//  $id: optional ID number of the entity to be retrieved
//  $filters: optional array of field => value pairs to filter on
function getResults($entity, $id = null, $filters = null) {
  // Define HTTP context
  $httpOptions = array('method' => 'GET', 'header' => 'Accept: text/xml', 'timeout' => '10');
  $httpContext = stream_context_create(array('http' => $httpOptions));

  // Build URL
  $url = WS_URL.$entity;
  if (!is_null($id) && is_numeric($id)) {
    $url .= "/$id";
  } elseif (!is_null($filters) && is_array($filters)) {
    $fURLpieces = array();
    foreach ($filters as $n => $v) {
      if (is_array($v)) {
	foreach ($v as $vs) $fURLpieces[] = "$n\[\]=$vs";
      } else {
	$fURLpieces[] = "$n=$v";
      }
    }

    $url .= '?'.implode('&', $fURLpieces);
  }

  // Get XML
  if ($filec = file_get_contents($url, false, $httpContext)) {
    $doc = new DOMDocument();
    $doc->loadXML($filec);
    return $doc;
  } else {
    return false;
  }
}

function getEmpty($entity) {
  // Define HTTP context
  $httpOptions = array('method' => 'GET', 'header' => 'Accept: text/xml', 'timeout' => '10');
  $httpContext = stream_context_create(array('http' => $httpOptions));

  // Build URL
  $url = WS_URL.$entity.'/_';

  // Get XML
  if ($filec = file_get_contents($url, false, $httpContext)) {
    $doc = new DOMDocument();
    $doc->loadXML($filec);
    return $doc;
  } else {
    return false;
  }
}

function getTransformed($entity, $xsl, $id = null, $filters = null, $params = array()) {
  // Get XML DOMDocument
  if ($doc = getResults($entity, $id, $filters)) {
    // Apply XSLT to XML and return HTML
    $html = processXSLT($doc, XSL_PATH.$xsl, $params);
    return $html->saveHTML();
  } else {
    return '';
  }
}

function isLoggedIn() {
  return isset($_SESSION['PDB']['ACCESS']);
}