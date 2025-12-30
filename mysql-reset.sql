ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'admin_password_2025';
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'admin_password_2025';
CREATE USER IF NOT EXISTS 'makis_ead_user'@'%' IDENTIFIED WITH mysql_native_password BY 'admin_password_2025';
GRANT ALL PRIVILEGES ON makis_ead_db.* TO 'makis_ead_user'@'%';
FLUSH PRIVILEGES;
