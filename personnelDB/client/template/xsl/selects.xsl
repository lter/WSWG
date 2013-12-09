<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output method="html" doctype-public="-//W3C//DTD HTML 4.01//EN" doctype-system="http://www.w3.org/TR/html4/strict.dtd" />

  <!-- generate HTML select entity from XML -->
  <xsl:template match="/">
    <xsl:apply-templates select="*"/>
  </xsl:template>

  <!-- processing for site lists -->
  <xsl:template match="siteList">
    <select name="site">
      <option value=""> -- All Sites -- </option>
      <xsl:for-each select="site">
	<xsl:sort select="siteAcronym" order="ascending"/>
	<option>
	  <xsl:attribute name="value">
	    <xsl:value-of select="siteAcronym"/>
	  </xsl:attribute>
	  <xsl:value-of select="siteAcronym"/>
	</option>
      </xsl:for-each>
    </select>
  </xsl:template>

  <!-- processing for roleType lists -->
  <xsl:template match="roleTypeList">
    <select name="roleType">
      <option value=""> -- All Role Types -- </option>
      <xsl:for-each select="roleType">
	<xsl:sort select="type" order="descending"/>
	<option>
	  <xsl:attribute name="value">
	    <xsl:value-of select="roleName"/>
	  </xsl:attribute>
	  <xsl:value-of select="roleName"/>
	</option>
      </xsl:for-each>
    </select>
  </xsl:template>

</xsl:stylesheet>