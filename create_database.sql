CREATE DATABASE my_library;
CREATE USER 'my_library_user'@'localhost' IDENTIFIED BY '123456';
GRANT ALL PRIVILEGES ON my_library.* TO 'my_library_user'@'localhost';
FLLUSH PRIVILEGES;