-- MySQL Database Security Initialization
-- Run automatically on first container start

-- ============================================
-- Remove anonymous users
-- ============================================
DELETE FROM mysql.user WHERE User='';

-- ============================================
-- Remove test database
-- ============================================
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';

-- ============================================
-- Create application database
-- ============================================
CREATE DATABASE IF NOT EXISTS aitechhub_store
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- ============================================
-- Create application user with limited privileges
-- ============================================
CREATE USER IF NOT EXISTS 'aitechhub'@'%' IDENTIFIED BY 'changeme_production_password';

-- Grant only necessary privileges
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, INDEX, ALTER, DROP, REFERENCES
    ON aitechhub_store.*
    TO 'aitechhub'@'%';

-- ============================================
-- Create read-only user for analytics/reporting
-- ============================================
CREATE USER IF NOT EXISTS 'aitechhub_readonly'@'%' IDENTIFIED BY 'readonly_password';

-- Grant only SELECT privilege
GRANT SELECT ON aitechhub_store.* TO 'aitechhub_readonly'@'%';

-- ============================================
-- Create backup user
-- ============================================
CREATE USER IF NOT EXISTS 'aitechhub_backup'@'localhost' IDENTIFIED BY 'backup_password';

-- Grant backup privileges
GRANT SELECT, LOCK TABLES, SHOW VIEW, EVENT, TRIGGER
    ON aitechhub_store.*
    TO 'aitechhub_backup'@'localhost';

-- ============================================
-- Flush privileges
-- ============================================
FLUSH PRIVILEGES;

-- ============================================
-- Additional security settings
-- ============================================

-- Disable LOAD DATA LOCAL INFILE
SET GLOBAL local_infile = 0;

-- Set password expiration policy
ALTER USER 'root'@'%' PASSWORD EXPIRE NEVER;
ALTER USER 'aitechhub'@'%' PASSWORD EXPIRE NEVER;

-- Account locking after failed attempts (MySQL 8.0+)
ALTER USER 'aitechhub'@'%' FAILED_LOGIN_ATTEMPTS 5 PASSWORD_LOCK_TIME 2;

-- ============================================
-- Create audit logging table
-- ============================================
USE aitechhub_store;

CREATE TABLE IF NOT EXISTS audit_log (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NULL,
    action VARCHAR(255) NOT NULL,
    table_name VARCHAR(255),
    record_id BIGINT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Create security events table
-- ============================================
CREATE TABLE IF NOT EXISTS security_events (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(100) NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'low',
    user_id BIGINT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    event_data JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_type (event_type),
    INDEX idx_severity (severity),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Create session tracking table
-- ============================================
CREATE TABLE IF NOT EXISTS active_sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    payload TEXT,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Database security initialization complete' AS Status;
