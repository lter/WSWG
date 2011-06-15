/*******************
*
* JavaScript functions for the PersonnelDB interface
*
*******************/

var PersonnelDB = new Object();

PersonnelDB.addRole = function(mode) {
    var target = document.getElementById('role-list').firstChild;
    var roleCount = document.getElementById('role-count');
    
    var rTemplate = document.getElementById(mode+'RoleType_template');
    var sTemplate = document.getElementById('siteAcronym_template');

    // Increment role count
    var x = parseInt(roleCount.value) + 1;
    roleCount.value = x;

    // TR to hold new role
    var tr = document.createElement('tr');

    // Clone role type select
    var roleTD = tr.appendChild(document.createElement('td'));
    var roleType = roleTD.appendChild(rTemplate.cloneNode(true));
    roleType.name = 'roleType_'+x;

    // Add hidden type input
    var type = roleTD.appendChild(document.createElement('input'));
    type.type = 'hidden';
    type.name = 'type_'+x;
    type.value = mode;

    // Clone site select
    var siteTD = tr.appendChild(document.createElement('td'));
    var site = siteTD.appendChild(sTemplate.cloneNode(true));
    site.name = 'roleSiteAcronym_'+x;

    // Add begin date input
    var beginTD = tr.appendChild(document.createElement('td'));
    beginTD.innerHTML = '<input type="text" name="beginDate_'+x+'"/>';

    // Add end date input
    var endTD = tr.appendChild(document.createElement('td'));
    endTD.innerHTML = '<input type="text" name="endDate_'+x+'"/>';

    // Add isActive checkbox
    var activeTD = tr.appendChild(document.createElement('td'));
    activeTD.innerHTML = '<input type="checkbox" name="roleIsActive_'+x+'"/>';

    target.appendChild(tr);
};


PersonnelDB.addContact = function() {
    var target = document.getElementById('contact-list').firstChild;
    var contactCount = document.getElementById('contact-count');
    
    var sTemplate = document.getElementById('siteAcronym_template');

    // Increment role count
    var x = parseInt(contactCount.value) + 1;
    contactCount.value = x;

    // TR to hold new role
    var tr = document.createElement('tr');

    // Basic information
    var infoTD = tr.appendChild(document.createElement('td'));
    var infoTBL = infoTD.appendChild(document.createElement('table'));

    var labelTR = infoTBL.appendChild(document.createElement('tr'));
    labelTR.innerHTML = '<th>Label: </th><td><input type="text" name="label_'+x+'/"></td>';

    var siteTR = infoTBL.appendChild(document.createElement('tr'));
    siteTR.innerHTML = '<th>Site: </th>';

    var siteTD = siteTR.appendChild(document.createElement('td'));
    var site = siteTD.appendChild(sTemplate.cloneNode(true));
    site.name = 'contactSiteAcronym_'+x;

    var activeTR = infoTBL.appendChild(document.createElement('tr'));
    activeTR.innerHTML = '<td colspan="2"><input type="checkbox" name="contactIsActive_'+x+'"/> Active contact information</td>';

    var primaryTR = infoTBL.appendChild(document.createElement('tr'));
    primaryTR.innerHTML = '<td colspan="2"><input type="checkbox" name="contactIsPrimary_'+x+'"/> Primary contact information</td>';
    
    // Address information
    var addressTD = tr.appendChild(document.createElement('td'));
    var addressTBL = addressTD.appendChild(document.createElement('table'));

    var addressTR = addressTBL.appendChild(document.createElement('tr'));
    addressTR.innerHTML = '<th>Address: </th><td><textarea name="address_'+x+'"></textarea></td>';

    var instTR = addressTBL.appendChild(document.createElement('tr'));
    instTR.innerHTML = '<th>Institution: </th><td><input type="text" name="institution_'+x+'"/></td>';

    var cityTR = addressTBL.appendChild(document.createElement('tr'));
    cityTR.innerHTML = '<th>City: </th><td><input type="text" name="city_'+x+'"/></td>';

    var adminTR = addressTBL.appendChild(document.createElement('tr'));
    adminTR.innerHTML = '<th>State/Province: </th><td><input type="text" name="administrativeArea_'+x+'"/></td>';

    var postalTR = addressTBL.appendChild(document.createElement('tr'));
    postalTR.innerHTML = '<th>Postal Code: </th><td><input type="text" name="postalCode_'+x+'"/></td>';

    var countryTR = addressTBL.appendChild(document.createElement('tr'));
    countryTR.innerHTML = '<th>Country: </th><td><input type="text" name="country_'+x+'"/></td>';

    // Phone, fax, and email
    var contactTD = tr.appendChild(document.createElement('td'));
    var contactTBL = contactTD.appendChild(document.createElement('table'));

    var emailTR = contactTBL.appendChild(document.createElement('tr'));
    emailTR.innerHTML = '<th>Email: </th><td><textarea name="email_'+x+'"></textarea></td>';

    var phoneTR = contactTBL.appendChild(document.createElement('tr'));
    phoneTR.innerHTML = '<th>Phone: </th><td><textarea name="phone_'+x+'"></textarea></td>';

    var faxTR = contactTBL.appendChild(document.createElement('tr'));
    faxTR.innerHTML = '<th>Fax: </th><td><textarea name="fax_'+x+'"></textarea></td>';


    target.appendChild(tr);
}