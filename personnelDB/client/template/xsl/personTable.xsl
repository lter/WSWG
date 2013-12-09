<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output method="html" doctype-public="-//W3C//DTD HTML 4.01//EN" doctype-system="http://www.w3.org/TR/html4/strict.dtd" />

  <!-- override this param to show inactive roles -->
  <xsl:param name="showInactive" select="false()"/>

  <!-- override this param to show edit links -->
  <xsl:param name="isLoggedIn" select="false()"/>

  <!-- generate HTML table entity from XML -->
  <xsl:template match="/">
    <table style="width: 100%">
      <xsl:attribute name='class'>search-results</xsl:attribute>

      <!-- table headers -->
      <tr>
	<th>Name</th>
	<th>Primary Email</th>
	<th>Role</th>
	<th>Site</th>
	<xsl:if test="$isLoggedIn">
	  <th><xsl:text> </xsl:text></th>
	</xsl:if>
      </tr>

      <xsl:for-each select="personnel/person">
	<!-- sort nodes by lastName prior to rendering html -->
	<xsl:sort select="identity/lastName"/>

	<xsl:variable name="row-class">
	  <xsl:choose>
	    <xsl:when test="position() mod 2 = 1">odd</xsl:when>
	    <xsl:otherwise>even</xsl:otherwise>
	  </xsl:choose>
	</xsl:variable>
		
	<xsl:choose>
	  <xsl:when test="$showInactive = true()">
	    <!-- include all roles, even if they're inactive -->
	    <xsl:for-each select="roleList/role">
	      <!-- display active roles first, then NSF roles first -->
	      <xsl:sort select="isActive" order="descending"/>
	      <xsl:sort select="roleType/@type" order="descending"/>

	      <tr>
		<xsl:attribute name="class"><xsl:value-of select="$row-class"/></xsl:attribute>		

		<!-- on the first role, show identity information -->
		<xsl:if test="position() = 1">
		  <xsl:apply-templates select="../../identity">
		    <xsl:with-param name="rows" select="count(../role)"/>
		  </xsl:apply-templates>
		</xsl:if>
		<xsl:apply-templates select="."/>



<!-- SHOW EDIT LINK FOR ADMIN -->
		<xsl:if test="$isLoggedIn">
		  <!-- on the first role, show edit link -->
		  <xsl:if test="position() = 1">
		    <td style="vertical-align: middle;">
		      <xsl:attribute name="rowspan">
			<xsl:value-of select="1"/>
		      </xsl:attribute>
		      <a>
			<xsl:attribute name="href">
			  management/edit.php?pid=<xsl:value-of select="../../personID"/>
			</xsl:attribute>
			Edit
		      </a>
		    </td>
		  </xsl:if>
		</xsl:if>
<!-- END SHOW EDIT LINK FOR ADMIN-->
	
		
	      </tr>
	    </xsl:for-each>
	  </xsl:when>
	  <xsl:otherwise>
	    <!-- include only active roles -->
	    <xsl:for-each select="roleList/role[isActive=1]">
	      <!-- display NSF roles first -->
	      <xsl:sort select="roleType/@type" order="descending"/>
  
	      <tr>
		<xsl:attribute name="class"><xsl:value-of select="$row-class"/></xsl:attribute>		

		<!-- on the first role, show identity information -->
		<xsl:if test="position() = 1">
		  <xsl:apply-templates select="../../identity">
		    <xsl:with-param name="rows" select="count(../role[isActive=1])"/>
		  </xsl:apply-templates>
		</xsl:if>

		<xsl:apply-templates select="."/>

		<xsl:if test="$isLoggedIn">
		  <!-- on the first role, show edit link -->
		  <xsl:if test="position() = 1">
		    <td style="vertical-align: middle;">
		      <xsl:attribute name="rowspan">
			<xsl:value-of select="count(../role[isActive=1])"/>
		      </xsl:attribute>
		      <a>
			<xsl:attribute name="href">
			  management/edit.php?pid=<xsl:value-of select="../../personID"/>
			</xsl:attribute>
			Edit
		      </a>
		    </td>
		  </xsl:if>
		</xsl:if>

	      </tr>
	    </xsl:for-each>
	  </xsl:otherwise>
	</xsl:choose>

      </xsl:for-each>
    </table>
  </xsl:template>

  <!-- identity template -->
  <xsl:template match="//identity">
    <xsl:param name="rows"/>
    <td>
      <xsl:attribute name="rowspan"><xsl:value-of select="$rows"/></xsl:attribute>
      <a>
	<xsl:attribute name="href">
	  <xsl:text>/personnelDB/view.php?pid=</xsl:text>
	  <xsl:value-of select="../personID"/>
	</xsl:attribute>
	
	<xsl:choose>
	  <!-- use perferred name if defined -->
	  <xsl:when test='preferredName'>
	    <xsl:value-of select="firstName"/>
	    <xsl:text> </xsl:text>
	    <xsl:value-of select="middleName"/>
	    <xsl:text> </xsl:text>
	    <xsl:value-of select="lastName"/>
	    <xsl:text> (</xsl:text>
	    <xsl:value-of select="preferredName"/>
	    <xsl:text>)</xsl:text>
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



      </a>
    </td>
    <td>
      <xsl:attribute name="rowspan"><xsl:value-of select="$rows"/></xsl:attribute>
      <a>
	<xsl:attribute name="href">mailto:<xsl:value-of select="primaryEmail"/></xsl:attribute>
	<xsl:value-of select="primaryEmail"/>
      </a>
    </td>
  </xsl:template>

  <!-- role template -->
  <xsl:template match="//role">
    <td>
      <xsl:if test="isActive = 0">
	<xsl:attribute name="class">inactive</xsl:attribute>
      </xsl:if>
      <xsl:value-of select="roleType"/>
    </td>
    <td>
      <xsl:if test="isActive = 0">
	<xsl:attribute name="class">inactive</xsl:attribute>
      </xsl:if>
      <xsl:value-of select="siteAcronym"/>
    </td>
  </xsl:template>

</xsl:stylesheet>