-- Add new fields to users table for demographic info
ALTER TABLE users
ADD COLUMN gender VARCHAR(20) NULL AFTER last_name,
ADD COLUMN nationality VARCHAR(100) NULL AFTER gender,
ADD COLUMN profession VARCHAR(100) NULL AFTER nationality,
ADD COLUMN date_of_birth DATE NULL AFTER profession;

-- Add new fields to participants table for serial number and category
ALTER TABLE participants
ADD COLUMN serial_number VARCHAR(32) NULL AFTER id,
ADD COLUMN category VARCHAR(50) NULL AFTER registration_status;

-- Optionally, update existing participants with serial numbers (example for MySQL)
-- SET @rownum := 0;
-- UPDATE participants SET serial_number = CONCAT('CONF2024-', LPAD((@rownum := @rownum + 1), 3, '0')) ORDER BY id; 