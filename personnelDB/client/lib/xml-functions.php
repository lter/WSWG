<?php

// xml-functions.php
//  Convenience functions for interacting with XML DOMDocuments

// Apply an XPath expression to a DOMDocument and return the results
function processXPath($doc, $exp, $context = null) {
  // Create XPath processor and run query
  $xpath = new DOMXPath($doc);
  $result = $xpath->query($exp, $context);

  // Return result if successful and non-empty
  if (!$result || $result->length == 0) {
    return false;
  } else {
    return $result;
  }
}

// Apply an XSLT to a DOMDocument and return the results
function processXSLT($doc, $xslPath, $params = array()) {
  // Load stylesheet
  $xsl = new DOMDocument();
  $xsl->load($xslPath);

  // Create XSLT processor
  $proc = new XSLTProcessor();
  $proc->importStyleSheet($xsl);

  // Register params
  foreach ($params as $n => $v) {
    $proc->setParameter('', $n, $v);
  }

  // Process and return DOMDocument
  return $proc->transformToDoc($doc);
}