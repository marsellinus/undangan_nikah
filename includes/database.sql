-- SQL Script for Creating and Setting Up Wedding Invitation Database
-- Database: undangan_nikah

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `undangan_nikah` CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Use the database
USE `undangan_nikah`;

-- Create RSVP table
CREATE TABLE IF NOT EXISTS `rsvp` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `attending` ENUM('yes', 'no') NOT NULL,
  `guest_count` INT(2) DEFAULT 1,
  `message` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create messages table with approval system
CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `is_approved` TINYINT(1) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Optional: Insert some sample data for testing
-- Sample RSVP entries
INSERT INTO `rsvp` (`name`, `attending`, `guest_count`, `message`) VALUES
('John Smith', 'yes', 2, 'Looking forward to celebrating with you!'),
('Jane Doe', 'no', 0, 'Unfortunately we cannot make it, but congratulations!');

-- Sample messages
INSERT INTO `messages` (`name`, `message`, `is_approved`) VALUES
('Anna Johnson', 'Wishing you a lifetime of love and happiness!', 1),
('Michael Brown', 'Congratulations on your special day!', 1),
('Sarah Wilson', 'May your love continue to grow each day!', NULL);

-- Create an admin user table (optional, if you need admin login for message approval)
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert default admin user (change password before deployment)
-- Default credentials: admin / password (this is hashed using PASSWORD function)
INSERT INTO `admin_users` (`username`, `password`) VALUES
('admin', SHA2('password', 256));

-- Add indexes for better performance
ALTER TABLE `rsvp` ADD INDEX `idx_attending` (`attending`);
ALTER TABLE `messages` ADD INDEX `idx_approved` (`is_approved`);