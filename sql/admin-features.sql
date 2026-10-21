-- Run once against the application's configured MySQL database.
-- Existing application tables are not modified by this migration.

CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
    `log_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_id` BIGINT NULL,
    `admin_name` VARCHAR(190) NOT NULL,
    `action` VARCHAR(80) NOT NULL,
    `details` VARCHAR(1000) NOT NULL,
    `target_user_id` BIGINT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    KEY `idx_activity_created` (`created_at`),
    KEY `idx_activity_admin` (`admin_id`),
    KEY `idx_activity_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `login_history` (
    `login_history_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `account_type` VARCHAR(16) NOT NULL,
    `account_id` BIGINT NULL,
    `account_identifier` VARCHAR(190) NOT NULL,
    `success` TINYINT(1) NOT NULL DEFAULT 0,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(512) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`login_history_id`),
    KEY `idx_login_created` (`created_at`),
    KEY `idx_login_type_result` (`account_type`, `success`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_settings` (
    `setting_key` VARCHAR(64) NOT NULL,
    `setting_value` VARCHAR(255) NOT NULL DEFAULT '',
    `updated_by` BIGINT NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `app_settings` (`setting_key`, `setting_value`)
VALUES ('app_name', 'EARN-NOW'), ('support_email', '');
