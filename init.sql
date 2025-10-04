CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    token VARCHAR(6) DEFAULT NULL,
    expired_duration INT DEFAULT 2,
    expired_date DATETIME DEFAULT NULL
);

-- Create the 'settings' table for global configurations
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(50) NOT NULL UNIQUE,
    `value` VARCHAR(255) NOT NULL
);

-- Insert the initial OTP duration value
INSERT INTO settings (`key`, `value`) VALUES ('otp_duration', '2');

-- Remove the expired_duration column from the 'users' table
ALTER TABLE users DROP COLUMN expired_duration;