<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:import href="gce_main_public.xsl" />
<xsl:output method="xml" omit-xml-declaration="yes" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" />

    <!-- call main template to generate page layout and scaffolding, which calls topnav and body templates at appropriate points in doc -->
    <xsl:template match="/">
        <xsl:call-template name="main">
            <xsl:with-param name="url_css">http://gce-lter.marsci.uga.edu/public/css/lter_personnel_list.css</xsl:with-param>
            <!-- note: a url_js parameter is also supported but not included here -->
        </xsl:call-template>
    </xsl:template>

    <!-- template for top bread-crumb navigation -->
    <xsl:template name="topnav">
        <!-- this is page-level navigation that will be inserted into the generic page scaffolding -->
        <a href="http://www.lternet.edu">LTER Home</a><xsl:text> &#62; </xsl:text>
        <a href="http://www.lternet.edu/im">Information Management</a><xsl:text> &#62; </xsl:text>
        <a href="http://intranet.lternet.edu/im/projects/webservices">Web Services Working Group</a><xsl:text> &#62; </xsl:text>
        <span class="current-page">PersonnelDB demo</span>
    </xsl:template>

    <!-- template for page contents  -->
    <xsl:template name="body">
        <!-- open a div and table for displaying the list-->
        <div id="personnel-list">
            <table>
                <tr>
                    <th>Network Role</th>
                    <th>Name</th>
                    <th>Institution</th>
                    <th>Email Address</th>
                </tr>
                <!-- serially call template to add rows, filtering by NSF role to enforce specific role ordering -->
                <xsl:call-template name="build-table">
                    <xsl:with-param name="nsf_role">Lead Principal Investigator</xsl:with-param>
                </xsl:call-template>
                <xsl:call-template name="build-table">
                    <xsl:with-param name="nsf_role">co-Principal Investigator</xsl:with-param>
                </xsl:call-template>
                <xsl:call-template name="build-table">
                    <xsl:with-param name="nsf_role">Faculty Associate</xsl:with-param>
                </xsl:call-template>
                <xsl:call-template name="build-table">
                    <xsl:with-param name="nsf_role">Postdoctoral Associate</xsl:with-param>
                </xsl:call-template>
                <xsl:call-template name="build-table">
                    <xsl:with-param name="nsf_role">Other Professional</xsl:with-param>
                </xsl:call-template>
                <xsl:call-template name="build-table">
                    <xsl:with-param name="nsf_role">Staff</xsl:with-param>
                </xsl:call-template>
                <xsl:call-template name="build-table">
                    <xsl:with-param name="nsf_role">Other Staff</xsl:with-param>
                </xsl:call-template>
                <xsl:call-template name="build-table">
                    <xsl:with-param name="nsf_role">Graduate Student</xsl:with-param>
                </xsl:call-template>
            </table>
        </div>
    </xsl:template>

    <!-- template for adding personnel records, filtering by NSF role and then sorting by last name -->
    <xsl:template name="build-table">
        <xsl:param name="nsf_role">*</xsl:param>
        <xsl:for-each select="personnel/person[roleList/role[roleType/@type='nsf' and roleType=$nsf_role and (isActive='true' or isActive=1)]][contactInfoList/contactInfo[(isPrimary='true' or isPrimary=1) and (isActive='true' or isActive=1)]]">
            <xsl:sort select="identity/lastName"/> <!-- sort nodes by lastName prior to rendering html -->
            <tr>
                <!-- check for first instance of a new NSF role for display, otherwise omit the site and role from the table -->
                <xsl:choose>
                    <xsl:when test="position() = 1">
                        <td><xsl:value-of select="roleList/role[roleType/@type='nsf' and (isActive=1 or isActive='true')]/siteAcronym"/><xsl:text>&#160;</xsl:text>
                            <xsl:value-of select="roleList/role[roleType/@type='nsf' and (isActive=1 or isActive='true')]/roleType"/></td>
                        <td><xsl:value-of select="identity/preferredName"/></td>
                        <td><xsl:value-of select="contactInfoList/contactInfo[isPrimary=1 or isPrimary=0]/institution"/></td>
                        <td><a><xsl:attribute name="href">mailto:<xsl:value-of select="identity/primaryEmail"/></xsl:attribute><xsl:value-of select="identity/primaryEmail"/></a></td>
                    </xsl:when>
                    <xsl:otherwise>
                        <td>&#160;</td>
                        <td><xsl:value-of select="identity/preferredName"/></td>
                        <td><xsl:value-of select="contactInfoList/contactInfo[isPrimary=1 or isPrimary=0]/institution"/></td>
                        <td><a><xsl:attribute name="href">mailto:<xsl:value-of select="identity/primaryEmail"/></xsl:attribute><xsl:value-of select="identity/primaryEmail"/></a></td>
                    </xsl:otherwise>
                </xsl:choose>
            </tr>
        </xsl:for-each>
    </xsl:template>

</xsl:stylesheet>
