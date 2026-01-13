-- Create database for LMS SEMPAT
CREATE DATABASE IF NOT EXISTS sempat_lms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant privileges (for development only)
-- GRANT ALL PRIVILEGES ON sempat_lms.* TO 'root'@'localhost';
-- FLUSH PRIVILEGES;

-- Use the database
USE sempat_lms;

-- Show confirmation
SELECT 'Database sempat_lms created successfully!' AS message;
