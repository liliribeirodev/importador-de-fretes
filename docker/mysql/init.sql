CREATE USER IF NOT EXISTS 'importador'@'%' IDENTIFIED BY 'importador';
GRANT ALL PRIVILEGES ON `testello`.* TO 'importador'@'%';
FLUSH PRIVILEGES;
