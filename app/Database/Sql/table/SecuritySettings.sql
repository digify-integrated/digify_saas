DROP TABLE IF EXISTS security_settings;

CREATE TABLE security_settings(
	security_setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	security_setting_name VARCHAR(100) NOT NULL,
	value VARCHAR(1000) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_accounts(user_account_id)
);

CREATE INDEX security_settings_idx_security_setting_id ON security_settings(security_setting_id);

INSERT INTO security_settings (security_setting_name, value, last_log_by) VALUES ('Max Failed Login Attempt', 5, '1');
INSERT INTO security_settings (security_setting_name, value, last_log_by) VALUES ('Max Failed OTP Attempt', 5, '1');
INSERT INTO security_settings (security_setting_name, value, last_log_by) VALUES ('Password Expiry Duration', 180, '1');
INSERT INTO security_settings (security_setting_name, value, last_log_by) VALUES ('Session Timeout Duration', 240, '1');
INSERT INTO security_settings (security_setting_name, value, last_log_by) VALUES ('OTP Duration', 5, '1');
INSERT INTO security_settings (security_setting_name, value, last_log_by) VALUES ('Reset Password Token Duration (Minutes)', 10, '1');
INSERT INTO security_settings (security_setting_name, value, last_log_by) VALUES ('Registration Verification Token Duration (Minutes)', 180, '1');
INSERT INTO security_settings (security_setting_name, value, last_log_by) VALUES ('Base Lock Duration (Seconds)', 60, '1');