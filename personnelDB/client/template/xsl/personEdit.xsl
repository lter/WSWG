<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output method="html" doctype-public="-//W3C//DTD HTML 4.01//EN" doctype-system="http://www.w3.org/TR/html4/strict.dtd" />

  <xsl:template match="/">
    <form action="submit.php" method="POST">
      <input type="hidden" name="personID">
	<xsl:attribute name="value"><xsl:value-of select="//personnel/person/personID"/></xsl:attribute>
      </input>

      <div class="edit">
	<h2>Identity Information</h2>
	<xsl:apply-templates select="//personnel/person/identity"/>
      </div>
      
      <div class="edit">
	<h2>Roles </h2>
	<xsl:apply-templates select="//personnel/person/roleList"/>
	<button type="button" class="new" onClick="PersonnelDB.addRole('nsf')">Add new NSF role</button>
	<button type="button" class="new" onClick="PersonnelDB.addRole('local')">Add new local role</button>
      </div>
      
      <div class="edit">
	<h2>Contact Information</h2>
	<xsl:apply-templates select="//personnel/person/contactInfoList"/>
	<button type="button" class="new" onClick="PersonnelDB.addContact()">Add new contact information</button>
      </div>

      <h2>
	<button type="submit">Submit</button>
      </h2>
    </form>

    <!-- hidden elements for cloning roles and contacts -->
    <div style="display: none">

      <xsl:call-template name="roleSelect">
	<xsl:with-param name="type" select="'nsf'"/>
	<xsl:with-param name="name" select="'nsfRoleType_template'"/>
      </xsl:call-template>

      <xsl:call-template name="roleSelect">
	<xsl:with-param name="type" select="'local'"/>
	<xsl:with-param name="name" select="'localRoleType_template'"/>
      </xsl:call-template>

      <xsl:call-template name="siteSelect">
	<xsl:with-param name="name" select="'siteAcronym_template'"/>
      </xsl:call-template>

    </div>
  </xsl:template>


  <!-- identity information -->
  <xsl:template match="//identity">
    <table>


	<tr>
	  <th>Username: </th>
	    <td>
		  <input type="text" name="ldapUser">
		  <xsl:attribute name="value"><xsl:value-of select="ldapUser"/></xsl:attribute>
		  </input>
		</td>
	  <th>Password</th>
    <td>
	<input type="password" name="ldapPass">
	<!--<input type="text" name="ldapPass">-->
		<xsl:attribute name="value"><xsl:value-of select="ldapPass"/></xsl:attribute>
		</input>
	</td>
    <td> </td>
	</tr>



      <tr>
	<th>Prefix: </th>
	<td>
	  <input type="text" name="prefix">
	    <xsl:attribute name="value"><xsl:value-of select="prefix"/></xsl:attribute>
	  </input>
	</td>
	<th>Title: </th>
	<td>
	  <input type="text" name="title">
	    <xsl:attribute name="value"><xsl:value-of select="title"/></xsl:attribute>
	  </input>
    </td>
	<td rowspan="5" style="text-align: center">
	  <!--
	  <span style="font-weight: bold">Aliases</span>
	  <xsl:text> </xsl:text>
	  <span class="instruction">(one per line)</span><br/>
	  <textarea name="nameAlias" style="height: 100px; width: 100%;">
	    <xsl:for-each select="nameAlias">
	      <xsl:value-of select="."/>
	      <xsl:text>&#10;</xsl:text>
	    </xsl:for-each>
	  </textarea>
	  -->
	</td>
      </tr>
      <tr>
	<th>First Name: </th>
	<td>
	  <input type="text" name="firstName">
	    <xsl:attribute name="value"><xsl:value-of select="firstName"/></xsl:attribute>
	  </input>
	</td>
	<th>Preferred Name: </th>
	<td>
	  <input type="text" name="preferredName">
	    <xsl:attribute name="value"><xsl:value-of select="preferredName"/></xsl:attribute>
	  </input>
	</td>
      </tr>
      <tr>
	<th>Middle Name: </th>
	<td>
	  <input type="text" name="middleName">
	    <xsl:attribute name="value"><xsl:value-of select="middleName"/></xsl:attribute>
	  </input>
	</td>
	<th>Primary Email: </th>
	<td>
	  <input type="text" name="primaryEmail">
	    <xsl:attribute name="value"><xsl:value-of select="primaryEmail"/></xsl:attribute>
	  </input>
	</td>
      </tr>
      <tr>
	<th>Last Name: </th>
	<td>
	  <input type="text" name="lastName">
	    <xsl:attribute name="value"><xsl:value-of select="lastName"/></xsl:attribute>
	  </input>
	</td>
	<td colspan="2" rowspan="2" style="text-align: center">
	  <input type="checkbox" value="1" name="optOut">
	    <xsl:if test="optOut = 1">
	      <xsl:attribute name="checked">checked</xsl:attribute>
	    </xsl:if>
	  </input>
	  Opt out
	</td>
      </tr>
      <tr>
	<th>Suffix: </th>
	<td>
	  <input type="text" name="suffix">
	    <xsl:attribute name="value"><xsl:value-of select="suffix"/></xsl:attribute>
	  </input>
	</td>
      </tr>
    </table>
  </xsl:template>


  <!-- role information -->
  <xsl:template match="//roleList">
    <input type="hidden" id="role-count" name="roleCount">
      <xsl:attribute name="value"><xsl:value-of select="count(role)"/></xsl:attribute>
    </input>

    <table id="role-list">
      <tbody>
	<tr>
	  <th>Role</th>
	  <th>Site</th>
	  <th>Begin Date <span class="instruction">(yyyy-mm-dd)</span></th>
	  <th>End Date <span class="instruction">(yyyy-mm-dd)</span></th>
	  <th>Is Active?</th>
	</tr>

	<xsl:for-each select="role">
	  <xsl:sort select="beginDate" order="descending"/>
	  <tr>
	    <td>
	      <input type="hidden">
		<xsl:attribute name="name">roleID_<xsl:value-of select="position()"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="roleID"/></xsl:attribute>
	      </input>

	      <xsl:call-template name="roleSelect">
		<xsl:with-param name="type" select="roleType/@type"/>
		<xsl:with-param name="selected" select="roleType"/>
		<xsl:with-param name="name" select="concat('roleType_',position())"/>
	      </xsl:call-template>

	      <input type="hidden">
		<xsl:attribute name="name">type_<xsl:value-of select="position()"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="roleType/@type"/></xsl:attribute>
	      </input>
	    </td>
	    <td>
	      <xsl:call-template name="siteSelect">
		<xsl:with-param name="selected" select="siteAcronym"/>
		<xsl:with-param name="name" select="concat('roleSiteAcronym_',position())"/>
	      </xsl:call-template>
	    </td>
	    <td>
	      <input type="text">
		<xsl:attribute name="name">beginDate_<xsl:value-of select="position()"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="beginDate"/></xsl:attribute>
	      </input>
	    </td>
	    <td>
	      <input type="text">
		<xsl:attribute name="name">endDate_<xsl:value-of select="position()"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="endDate"/></xsl:attribute>
	      </input>
	    </td>
	    <td>
	      <input type="checkbox" value="1">
		<xsl:attribute name="name">roleIsActive_<xsl:value-of select="position()"/></xsl:attribute>
		<xsl:if test="isActive = 1">
		  <xsl:attribute name="checked">checked</xsl:attribute>
		</xsl:if>
	      </input>
	    </td>
	  </tr>
	</xsl:for-each>
      </tbody>
    </table>
  </xsl:template>


  <!-- contact information -->
  <xsl:template match="//contactInfoList">
    <input type="hidden" id="contact-count" name="contactCount">
      <xsl:attribute name="value"><xsl:value-of select="count(contactInfo)"/></xsl:attribute>
    </input>

    <table id="contact-list">
      <tbody>
	<xsl:for-each select="contactInfo">
	  <xsl:sort select="isPrimary" order="descending"/>
	  <xsl:sort select="beginDate" order="descending"/>
	  
	  <tr>
	    <td>
	      <input type="hidden">
		<xsl:attribute name="name">contactInfoID_<xsl:value-of select="position()"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="contactInfoID"/></xsl:attribute>
	      </input>

	      <!-- Contact info label and flags -->
	      <table>
		<tr>
		  <th>Label: </th>
		  <td>
		    <input type="text">
		      <xsl:attribute name="name">label_<xsl:value-of select="position()"/></xsl:attribute>
		      <xsl:attribute name="value"><xsl:value-of select="label"/></xsl:attribute>
		    </input>
		  </td>
		</tr>
		<tr>
		  <th>Site: </th>
		  <td>
		    <xsl:call-template name="siteSelect">
		      <xsl:with-param name="selected" select="siteAcronym"/>
		      <xsl:with-param name="name" select="concat('contactSiteAcronym_',position())"/>
		    </xsl:call-template>
		  </td>
		</tr>
		<tr>
		  <td colspan="2">
		    <input type="checkbox" value="1">
		      <xsl:attribute name="name">contactIsActive_<xsl:value-of select="position()"/></xsl:attribute>
		      <xsl:if test="isActive = 1">
			<xsl:attribute name="checked">checked</xsl:attribute>
		      </xsl:if>
		    </input>
		    Active contact information
		  </td>
		</tr>
		<tr>
		  <td colspan="2">
		    <input type="checkbox" value="1">
		      <xsl:attribute name="name">contactIsPrimary_<xsl:value-of select="position()"/></xsl:attribute>
		      <xsl:if test="isPrimary = 1">
			<xsl:attribute name="checked">checked</xsl:attribute>
		      </xsl:if>
		    </input>
		    Primary contact information
		  </td>
		</tr>
	      </table>
	    </td>
	    <td>

	      <!-- Address fields -->
	      <table>
		<tr>
		  <th>Address: </th>
		  <td>
		    <textarea>
		      <xsl:attribute name="name">address_<xsl:value-of select="position()"/></xsl:attribute>
		      <xsl:for-each select="address">
			<xsl:value-of select="."/>
			<xsl:text>&#10;</xsl:text>
		      </xsl:for-each>
		    </textarea>
		  </td>
		</tr>
		<tr>
		  <th>Institution: </th>
		  <td>
		    <input type="text">
		      <xsl:attribute name="name">institution_<xsl:value-of select="position()"/></xsl:attribute>
		      <xsl:attribute name="value"><xsl:value-of select="institution"/></xsl:attribute>
		    </input>
		  </td>
		</tr>
		<tr>
		  <th>City: </th>
		  <td>
		    <input type="text">
		      <xsl:attribute name="name">city_<xsl:value-of select="position()"/></xsl:attribute>
		      <xsl:attribute name="value"><xsl:value-of select="city"/></xsl:attribute>
		    </input>
		  </td>
		</tr>
		<tr>
		  <th>State/Province: </th>
		  <td>
		    <input type="text">
		      <xsl:attribute name="name">administrativeArea_<xsl:value-of select="position()"/></xsl:attribute>
		      <xsl:attribute name="value"><xsl:value-of select="administrativeArea"/></xsl:attribute>
		    </input>
		  </td>
		</tr>
		<tr>
		  <th>Postal Code: </th>
		  <td>
		    <input type="text">
		      <xsl:attribute name="name">postalCode_<xsl:value-of select="position()"/></xsl:attribute>
		      <xsl:attribute name="value"><xsl:value-of select="postalCode"/></xsl:attribute>
		    </input>
		  </td>
		</tr>
		<tr>
		  <th>Country: </th>
		  <td>
		    <input type="text">
		      <xsl:attribute name="name">country_<xsl:value-of select="position()"/></xsl:attribute>
		      <xsl:attribute name="value"><xsl:value-of select="country"/></xsl:attribute>
		    </input>
		  </td>
		</tr>
	      </table>
	    </td>
	    <td>

	      <!-- Phone and email fields -->
	      <table>
		<tr>
		  <th>Email: </th>
		  <td>
		    <textarea>
		      <xsl:attribute name="name">email_<xsl:value-of select="position()"/></xsl:attribute>
		      <xsl:for-each select="email">
			<xsl:value-of select="."/>
			<xsl:text>&#10;</xsl:text>
		      </xsl:for-each>
		    </textarea>
		  </td>
		</tr>
		<tr>
		  <th>Phone: </th>
		  <td>
		    <textarea>
		      <xsl:attribute name="name">phone_<xsl:value-of select="position()"/></xsl:attribute>
		      <xsl:for-each select="phone">
			<xsl:value-of select="."/>
			<xsl:text>&#10;</xsl:text>
		      </xsl:for-each>
		    </textarea>
		  </td>
		</tr>
		<tr>
		  <th>Fax: </th>
		  <td>
		    <textarea>
		      <xsl:attribute name="name">fax_<xsl:value-of select="position()"/></xsl:attribute>
		      <xsl:for-each select="fax">
			<xsl:value-of select="."/>
			<xsl:text>&#10;</xsl:text>
		      </xsl:for-each>
		    </textarea>
		  </td>
		</tr>
	      </table>
	    </td>
	  </tr>
	</xsl:for-each>
      </tbody>
    </table>
  </xsl:template>


  <!-- role type select -->
  <xsl:template name="roleSelect">
    <xsl:param name="type"/>
    <xsl:param name="selected"/>
    <xsl:param name="name"/>

    <select>
      <xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
      <xsl:attribute name="id"><xsl:value-of select="$name"/></xsl:attribute>

      <xsl:for-each select="/edit/roleTypeList/roleType[type=$type]">
	<option>
	  <xsl:attribute name="value">
	    <xsl:value-of select="roleName"/>
	  </xsl:attribute>
	  <xsl:if test="roleName = $selected">
	    <xsl:attribute name="selected">selected</xsl:attribute>
	  </xsl:if>
	  <xsl:value-of select="roleName"/>
	</option>
      </xsl:for-each>
    </select>

  </xsl:template>


  <!-- site select -->

  <xsl:template name="siteSelect">
    <xsl:param name="selected"/>
    <xsl:param name="name"/>

    <select>
      <xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
      <xsl:attribute name="id"><xsl:value-of select="$name"/></xsl:attribute>

      <xsl:for-each select="/edit/siteList/site">
	<xsl:sort select="siteAcronym" order="ascending"/>
	<option>
	  <xsl:attribute name="value">
	    <xsl:value-of select="siteAcronym"/>
	  </xsl:attribute>
	  <xsl:if test="siteAcronym = $selected">
	    <xsl:attribute name="selected">selected</xsl:attribute>
	  </xsl:if>
	  <xsl:value-of select="siteAcronym"/>
	</option>
      </xsl:for-each>
    </select>
    
  </xsl:template>
  
</xsl:stylesheet>
