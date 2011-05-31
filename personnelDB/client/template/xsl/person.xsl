<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output method="html" doctype-public="-//W3C//DTD HTML 4.01//EN" doctype-system="http://www.w3.org/TR/html4/strict.dtd" />

  <xsl:template match="/">
    <div id="identity">
      <xsl:apply-templates select="personnel/person/identity"/>
    </div>

    <div id="roles">
      <xsl:apply-templates select="personnel/person/roleList"/>
    </div>

    <div id="contacts">
      <xsl:apply-templates select="personnel/person/contactInfoList"/>
    </div>
  </xsl:template>

  <!-- identity information -->
  <xsl:template match="//identity">
    <h1>
      <xsl:choose>
	<!-- use perferred name if defined -->
	<xsl:when test='preferredName'>
	  <xsl:value-of select="preferredName"/>
	</xsl:when>
	<!-- otherwise, build name -->
	<xsl:otherwise>
	  <xsl:value-of select="firstName"/>
	  <xsl:text> </xsl:text>
	  <xsl:value-of select="middleName"/>
	  <xsl:text> </xsl:text>
	  <xsl:value-of select="lastName"/>
	</xsl:otherwise>
      </xsl:choose>
    </h1>
    <div id="primary-email">
      <a>
	<xsl:attribute name="href">
	  mailto:<xsl:value-of select="primaryEmail"/>
	</xsl:attribute>
	<xsl:value-of select="primaryEmail"/>
      </a>
    </div>
  </xsl:template>

  <!-- role information -->
  <xsl:template match="//roleList">
    <h2>Roles</h2>
    <xsl:for-each select="role">
      <xsl:sort select="beginDate" order="descending"/>
      <div>
	<xsl:choose>
	  <xsl:when test="isActive = 0">
	    <xsl:attribute name="class">role inactive</xsl:attribute>
	  </xsl:when>
	  <xsl:otherwise>
	    <xsl:attribute name="class">role</xsl:attribute>
	  </xsl:otherwise>
	</xsl:choose>

	<xsl:value-of select="roleType"/>, <xsl:value-of select="siteAcronym"/>
	<xsl:if test="beginDate">
	  (<xsl:value-of select="beginDate"/> &#8212; <xsl:value-of select="endDate"/>)
	</xsl:if>
      </div>
    </xsl:for-each>
  </xsl:template>

  <!-- contact information -->
  <xsl:template match="//contactInfoList">
    <h2>Contact Information</h2>
    <xsl:for-each select="contactInfo">
      <xsl:sort select="isPrimary" order="descending"/>
      <xsl:sort select="beginDate" order="descending"/>
      <div>
	<xsl:choose>
	  <xsl:when test="isActive = 0">
	    <xsl:attribute name="class">contact inactive</xsl:attribute>
	  </xsl:when>
	  <xsl:otherwise>
	    <xsl:attribute name="class">contact</xsl:attribute>
	  </xsl:otherwise>
	</xsl:choose>

	<h3>
	  <xsl:value-of select="label"/>
	  <xsl:if test="beginDate">
	    (<xsl:value-of select="beginDate"/> &#8212; <xsl:value-of select="endDate"/>)
	  </xsl:if>
	</h3>

	<!-- Display address information first (if present) -->
	<span><xsl:value-of select="institution"/></span>
	<xsl:for-each select="address">
	  <span><xsl:value-of select="."/></span>
	</xsl:for-each>
	<span>
	  <xsl:value-of select="city"/>
	  <xsl:text>, </xsl:text>
	  <xsl:value-of select="administrativeArea"/>
	  <xsl:text> </xsl:text>
	  <xsl:value-of select="postalCode"/>
	  <xsl:text> </xsl:text>
	  <xsl:value-of select="country"/>
	</span>

	<!-- Display email addresses -->
	<xsl:for-each select="email">
	  <span>
	    <a>
	      <xsl:attribute name="href">
		mailto:<xsl:value-of select="."/>
	      </xsl:attribute>
	      <xsl:value-of select="."/>
	    </a>
	  </span>
	</xsl:for-each>

	<!-- Display phone numbers -->
	<xsl:for-each select="phone">
	  <span>Phone: <xsl:value-of select="."/></span>
	</xsl:for-each>

	<!-- Display fax numbers -->
	<xsl:for-each select="fax">
	  <span>Fax: <xsl:value-of select="."/></span>
	</xsl:for-each>

      </div>
    </xsl:for-each>
  </xsl:template>

</xsl:stylesheet>