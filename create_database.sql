CREATE DATABASE my_library;
CREATE USER 'my_library_user'@'localhost' IDENTIFIED BY 'StRoNgPaSs123.!';
GRANT ALL PRIVILEGES ON my_library.* TO 'my_library_user'@'localhost';
FLUSH PRIVILEGES;