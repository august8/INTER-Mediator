#!/bin/bash

WEBROOT="/var/www/html"

IMROOT="${WEBROOT}/INTER-Mediator"
IMSUPPORT="${IMROOT}/INTER-Mediator-Support"
IMSAMPLE="${IMROOT}/Samples"
IMDISTDOC="${IMROOT}/dist-docs"
IMVMROOT="${IMROOT}/dist-docs/vm-for-trial"

aptitude update
aptitude full-upgrade
aptitude install sqlite --assume-yes
aptitude install acl --assume-yes
aptitude install libmysqlclient-dev --assume-yes
aptitude install php5-pgsql --assume-yes
aptitude install php5-sqlite --assume-yes
aptitude install php5-curl --assume-yes
aptitude install git --assume-yes
aptitude clean
group add im-developer
usermod -a -G im-developer developer
usermod -a -G im-developer www-data
passwd postgres #and input the password

cd "${WEBROOT}"
git clone https://github.com/msyk/INTER-Mediator.git

mv "${WEBROOT}/index.html" "${WEBROOT}/index_original.html"

cd "${IMSUPPORT}"
git clone https://github.com/codemirror/CodeMirror.git

cd "${WEBROOT}"
ln -s "${IMVMROOT}/index.html" index.html

find "${WEBROOT}" -type d -exec setfacl -d -m g:im-developer:rwx {} \;
find "${WEBROOT}" -type f -exec setfacl -m g:im-developer:rw {} \;
chown -R developer:im-developer "${WEBROOT}"
chmod -R g+w "${WEBROOT}"

echo 'AddType "text/html; charset=UTF-8" .html' > "${WEBROOT}/.htaccess"

echo '<?php' > "${WEBROOT}/params.php"
echo '$dbUser = "web";' >> "${WEBROOT}/params.php"
echo '$dbPassword = "password";' >> "${WEBROOT}/params.php"
echo '$dbDSN = "mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=test_db;";' >> "${WEBROOT}/params.php"
echo '$dbOption = array();' >> "${WEBROOT}/params.php"
echo '$browserCompatibility = array(' >> "${WEBROOT}/params.php"
echo '"Chrome" => "1+","FireFox" => "2+","msie" => "9+","Opera" => "1+",' >> "${WEBROOT}/params.php"
echo '"Safari" => "4+","Trident" => "5+",);' >> "${WEBROOT}/params.php"
echo '$dbServer = "192.168.56.1";' >> "${WEBROOT}/params.php"
echo '$dbPort = "80";' >> "${WEBROOT}/params.php"
echo '$dbDataType = "FMPro12";' >> "${WEBROOT}/params.php"
echo '$dbDatabase = "TestDB";' >> "${WEBROOT}/params.php"
echo '$dbProtocol = "HTTP";' >> "${WEBROOT}/params.php"


# Activate DefEdit/PageEdit

sed -E -e 's|//IM_Entry|IM_Entry|' "${IMSUPPORT}/defedit.php" > "${IMSUPPORT}/temp"
rm "${IMSUPPORT}/defedit.php"
mv "${IMSUPPORT}/temp" "${IMSUPPORT}/defedit.php"

sed -E -e 's|//IM_Entry|IM_Entry|' "${IMSUPPORT}/pageedit.php" > "${IMSUPPORT}/temp"
rm "${IMSUPPORT}/pageedit.php"
mv "${IMSUPPORT}/temp" "${IMSUPPORT}/pageedit.php"

# Copy Templates

for Num in $(seq 40)
do
    PadZero="00${Num}"
    DefFile="def${PadZero: -2}.php"
    PageFile="page${PadZero: -2}.html"
    sed -E -e "s|require_once('INTER-Mediator.php');|require_once('INTER-Mediator/INTER-Mediator.php');|" \
        "${IMSAMPLE}/templates/definition_file_simple.php" > "${WEBROOT}/${DefFile}"
    sed -E -e "s/definitin_file_simple.php/${DefFile}/" \
        "${IMSAMPLE}/templates/page_file_simple.html" > "${WEBROOT}/${PageFile}"
done

chmod -R g+rw "${WEBROOT}"

# Import schema

mysql -u root --password=im4135dev < "${IMDISTDOC}/sample_schema_mysql.txt"

echo "im4135dev" | sudo -u postgres -S psql -c 'create database test_db;'
echo "im4135dev" | sudo -u postgres -S psql -f "${IMDISTDOC}/sample_schema_pgsql.txt" test_db

mkdir -p /var/db/im
sqlite3 /var/db/im/sample.sq3 < "${IMDISTDOC}/sample_schema_sqlite.txt"
chown -R www-data /var/db/im