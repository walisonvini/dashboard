#!/bin/bash
set -e

# Create the testing database
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
CREATE DATABASE IF NOT EXISTS ${DB_DATABASE}_testing;
GRANT ALL PRIVILEGES ON ${DB_DATABASE}_testing.* TO '${MYSQL_USER}'@'%';
FLUSH PRIVILEGES;
EOSQL
