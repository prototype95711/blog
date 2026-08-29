#!/usr/bin/env bash
set -euo pipefail

DB_DATADIR=/var/lib/mysql
MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD:-changeme}"
MYSQL_DATABASE="${MYSQL_DATABASE:-blog}"
MYSQL_USER="${MYSQL_USER:-blog}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-changeme}"

chown -R mysql:mysql "$DB_DATADIR"

# First boot: initialize the MariaDB data directory, then bootstrap the
# root password, application database and application user.
if [ ! -d "$DB_DATADIR/mysql" ]; then
    echo "[entrypoint] Initializing MariaDB data directory..."
    mariadb-install-db --user=mysql --datadir="$DB_DATADIR" --auth-root-authentication-method=normal >/dev/null

    echo "[entrypoint] Bootstrapping database, app user and schema..."
    mariadbd --user=mysql --datadir="$DB_DATADIR" --skip-networking --socket=/run/mysqld/mysqld.sock &
    BOOTSTRAP_PID=$!

    for i in $(seq 1 30); do
        mariadb-admin --socket=/run/mysqld/mysqld.sock ping >/dev/null 2>&1 && break
        sleep 1
    done

    mariadb --socket=/run/mysqld/mysqld.sock -u root <<SQL
ALTER USER 'root'@'localhost' IDENTIFIED BY '${MYSQL_ROOT_PASSWORD}';
DELETE FROM mysql.user WHERE User='';
CREATE DATABASE IF NOT EXISTS \`${MYSQL_DATABASE}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${MYSQL_USER}'@'%' IDENTIFIED BY '${MYSQL_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${MYSQL_DATABASE}\`.* TO '${MYSQL_USER}'@'%';
FLUSH PRIVILEGES;
SQL

    if [ -f /docker-entrypoint-initdb.d/init.sql ]; then
        mariadb --socket=/run/mysqld/mysqld.sock -u root -p"${MYSQL_ROOT_PASSWORD}" < /docker-entrypoint-initdb.d/init.sql
    fi

    mariadb-admin --socket=/run/mysqld/mysqld.sock -u root -p"${MYSQL_ROOT_PASSWORD}" shutdown
    wait "$BOOTSTRAP_PID" 2>/dev/null || true
    echo "[entrypoint] Database bootstrap complete."
fi

exec "$@"
