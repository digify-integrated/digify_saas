DROP TABLE IF EXISTS user_accounts;

CREATE TABLE user_accounts (
    user_account_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    file_as VARCHAR(300) NOT NULL,
    email VARCHAR(255) UNIQUE,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    profile_picture VARCHAR(500) NULL,
    password_expiry_date DATE NOT NULL,
    active VARCHAR(5) DEFAULT 'Yes',
    two_factor_auth VARCHAR(5) DEFAULT 'No',
    last_failed_login_attempt DATETIME,
    failed_login_attempts INT DEFAULT 0,
    locked_duration INT DEFAULT 0,
    otp VARCHAR(255),
    otp_expiry_date DATETIME,
    failed_otp_attempts INT DEFAULT 0,
    last_password_change DATETIME,
    last_password_reset DATETIME,
    last_connection_date DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_accounts(user_account_id)
);

CREATE INDEX user_account_idx_email ON user_accounts(email);
CREATE INDEX user_account_idx_username ON user_accounts(username);
CREATE INDEX user_account_idx_active ON user_accounts(active);

INSERT INTO user_account (file_as, username, email, password, password_expiry_date) VALUES ('Digify Bot', 'digifybot', 'digifybot@gmail.com', 'Lu%2Be%2BRZfTv%2F3T0GR%2Fwes8QPJvE3Etx1p7tmryi74LNk%3D', '2025-12-31');
INSERT INTO user_account (file_as, username, email, password, password_expiry_date) VALUES ('Lawrence Agulto', 'ldagulto', 'lawrenceagulto.317@gmail.com', 'Lu%2Be%2BRZfTv%2F3T0GR%2Fwes8QPJvE3Etx1p7tmryi74LNk%3D', '2025-12-31');