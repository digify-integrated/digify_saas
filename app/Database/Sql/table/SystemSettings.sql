DROP TABLE IF EXISTS system_settings;

CREATE TABLE system_settings(
	system_setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	system_setting_name VARCHAR(100) NOT NULL,
	value VARCHAR(1000) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_accounts(user_account_id)
);

CREATE INDEX system_settings_idx_system_setting_id ON system_settings(system_setting_id);

INSERT INTO system_settings (system_setting_name, value, last_log_by) VALUES ('Allow Registration', 'Yes', '1');