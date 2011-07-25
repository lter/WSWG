<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
   
   <xsl:output method="xml" omit-xml-declaration="no" encoding="UTF-8"/>
   
   <!-- main template -->
   <xsl:template match="/">
      <xsl:apply-templates select="personnel"/>
   </xsl:template>
   
   <!-- match personnel with any active NSF role-->
   <xsl:template match="personnel">
      
      <!-- create root personnel element for xml document -->
      <xsl:element name="personnel">
         
         <!-- iterate through person elements without checking for active NSF role -->
         <xsl:for-each select="person">
            
            <!-- generate eml party element with identity and primary contact info from LTER personnelDB record -->
            <xsl:element name="creator">

               <!-- add individual name fields, checking for existance of optional elements -->
               <xsl:element name="individualName">
                  <xsl:if test="identity/prefix != ''">
                     <xsl:element name="salution">
                        <xsl:value-of select="identity/prefix"/>
                     </xsl:element>
                  </xsl:if>
                  <xsl:element name="givenName">
                     <xsl:value-of select="identity/firstName"/>
                  </xsl:element>
                  <xsl:if test="identity/middleName != ''">
                     <xsl:element name="givenName">
                        <xsl:value-of select="identity/middleName"/>
                     </xsl:element>
                  </xsl:if>
                  <xsl:element name="surName">
                     <xsl:value-of select="identity/lastName"/>
                  </xsl:element>
               </xsl:element>
               
               <!-- add title as positionName element -->
               <xsl:if test="identity/title != ''">
                  <xsl:element name="positionName">
                     <xsl:value-of select="identity/title"/>
                  </xsl:element>
               </xsl:if>
               
               <!-- call template to add organization, address, phone, email, url -->
               <xsl:apply-templates select="contactInfoList"/>
               
               <!-- add LTER personnel ID -->
               <xsl:element name="userId">
                  <xsl:value-of select="personID"/>
               </xsl:element>
               
            </xsl:element>
         </xsl:for-each>
      </xsl:element>
   </xsl:template>

   <!-- template for adding active primary contact info to EML party record -->
   <xsl:template match="contactInfoList">
      
      <!-- loop through active, primary contactInfo elements (should be only 1 per person) -->
      <xsl:for-each select="contactInfo[(isPrimary='true' or isPrimary=1) and (isActive='true' or isActive=1)]">
         
         <!-- add institution name as organization if defined -->
         <xsl:if test="institution != ''">
            <xsl:element name="organization">
               <xsl:value-of select="institution"/>
            </xsl:element>
         </xsl:if>
         
         <!-- add address lines, city, state, zip, country -->
         <xsl:element name="address">
            <xsl:for-each select="address">
               <xsl:element name="deliveryPoint">
                  <xsl:value-of select="."/>
               </xsl:element>
            </xsl:for-each>            
            <xsl:element name="city">
               <xsl:value-of select="city"/>
            </xsl:element>
            <xsl:element name="administrativeArea">
               <xsl:value-of select="administrativeArea"/>
            </xsl:element>
            <xsl:element name="postalCode">
               <xsl:value-of select="postalCode"/>
            </xsl:element>
            <xsl:element name="country">
               <xsl:value-of select="country"/>
            </xsl:element>
         </xsl:element>
         
         <!-- iterate through voice phone numbers -->
         <xsl:for-each select="phone">
            <xsl:element name="phone">
               <xsl:attribute name="type">voice</xsl:attribute>
               <xsl:value-of select="."/>
            </xsl:element>
         </xsl:for-each>
         
         <!-- iterate through fax numbers -->
         <xsl:for-each select="fax">
            <xsl:element name="phone">
               <xsl:attribute name="type">facsimile</xsl:attribute>
               <xsl:value-of select="."/>
            </xsl:element>
         </xsl:for-each>
         
         <!-- iterate through email addresses -->
         <xsl:for-each select="email">
            <xsl:element name="electronicMailAddress">
               <xsl:value-of select="."/>
            </xsl:element>
         </xsl:for-each>
         
       </xsl:for-each>
   </xsl:template>
   
</xsl:stylesheet>