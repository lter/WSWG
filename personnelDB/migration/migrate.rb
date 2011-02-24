require 'sequel'
require 'yaml'

credentials = File.open('mysql_credentials.yaml') {|y| YAML::load(y)}
DB_OLD = Sequel.connect(:adapter=>'mysql', :database=> 'LTER_dbo', 
     :user=> credentials['username'], :password  => credentials['password'])

DB_NEW = Sequel.connect(:adapter=>'mysql', :database=>'lter_personnel', 
     :user=> credentials['username'],:password  => credentials['password'])

old = DB_OLD[:person]
new = DB_NEW[:person]
contactInfo = DB_NEW[:contactInfo]
contactInfoField = DB_NEW[:contactInfoField]

contactInfoFieldType = DB_NEW[:contactInfoFieldType]
#[ 
#['phone','phone'], 
#['fax','phone'], 
#['email','electronicMailAddress'], 
#['address','deliveryPoint'],
#['city','city'],
#['state','administrativeArea'],
#['institution','organizationName'],
#['postalCode','postalCode'],
#['country','country']
#].each do |type|
#	contactInfoFieldType.insert(:contactInfoFieldType => type[0], :emlType=>type[1])
#end

old.each do |person|
	person_id = new.insert(:firstName => person[:firstname], 
	   :middleName => person[:middlename],
	   :lastname=>person[:lastname],
     :prefix => person[:nameprefix],
     :suffix => person[:namesuffix],
     :title => person[:title],
     :preferredName => person[:nickname],
     :primaryEmail => person[:primaryemail])

  contact_info_id = contactInfo.insert(:personID=>person_id,
		:isPrimary => true)

	address_type = contactInfoFieldType.find(:contactFieldInfoType=>'address')

  order = 0
  [:address1, :address2, :address3].each do |address|
    if person[address] 
      contactInfoField.insert(:contactInfoID => contact_info_id,
                              :value => person[address],
                              :sortOrder => order,
                              :contactInfoFieldTypeID => address_type[:id])
      order += 1
    end
  end

  if person[:city] then
    address_type = contactInfoFieldType.find(:contactFieldInfoType=>'city')
    contactInfoField.insert(:contactInfoID => contact_info_id,
          :value => person[:city],
          :contactInfoFieldTypeID => address_type[:id])
  end

  if person[:state] then
    address_type = contactInfoFieldType.find(:contactFieldInfoType=>'state')
    contactInfoField.insert(:contactInfoID => contact_info_id,
                            :value => person[:state],
                            :contactInfoFieldTypeID => address_type[:id])
  end
  
  if person[:zip] then
    address_type = contactInfoFieldType.find(:contactInfoFieldType => 'postalCode')
    contactInfoField.insert(:contactInfoID=> contact_info_id,
                           :value=>person[:zip],
                           :contactInfoFieldTypeID => address_type[:id])
  end

  if person[:county] then
    address_type = contactInfoFieldType.find(:contactInfoFieldType=>'county')
    contactInfoField.insert(:contactInfoID=> contact_info_id,
                           :value=>person[:county],
                           :contactInfoFieldTypeID => address_type[:id])
  end

  address_type = contactInfoFieldType.find(:contactInfoFieldType=>'phone')
  order = 0
  [:phone1, :phone2].each do |phone|
    if person[phone] then
      contactInfoField.insert(:contactInfoID=> contact_info_id,
                             :value=>person[phone],
                             :sortOrder => order,
                             :contactInfoFieldTypeID => address_type[:id])
      order += 1
    end
  end

  order = 0
  [:email1, :email2].each do |email|
    if person[email] then
      contactInfoField.insert(:contactInfoID=> contact_info_id,
                             :value=>person[email],
                             :sortOrder => order,
                             :contactInfoFieldTypeID => address_type[:id])
      order += 1
    end
  end
end

old_site = DB_OLD[:site_oranizations]
new_site = DB_NEW[:site]

#DB_OLD.fetch("select distinct siteidtext from site_organization where organization = 'LTER'") do |row|
#  new_site.insert(:siteAcronym=> row[:siteidtext])
#end

