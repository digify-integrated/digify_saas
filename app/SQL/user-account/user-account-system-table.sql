DROP TABLE IF EXISTS user_account;

CREATE TABLE user_account (
    user_account_id CHAR(36) PRIMARY KEY NOT NULL,
    file_as VARCHAR(300) NOT NULL,
    email VARCHAR(255),
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    profile_picture VARCHAR(500) NULL,
    password_expiry_date DATE NOT NULL,
    locked VARCHAR(5) DEFAULT 'No',
    active VARCHAR(5) DEFAULT 'Yes',
    receive_notification VARCHAR(5) DEFAULT 'Yes',
    two_factor_auth VARCHAR(5) DEFAULT 'Yes',
    multiple_session VARCHAR(5) DEFAULT 'Yes',
    last_failed_login_attempt DATETIME,
    failed_login_attempts INT(1),
    reset_token VARCHAR(255),
    reset_token_expiry_date DATETIME,
    session_token VARCHAR(255) PRIMARY KEY,
    otp VARCHAR(255),
    otp_expiry_date DATETIME,
    failed_otp_attempts INT(1),
    last_password_change DATETIME,
    last_password_reset DATETIME,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX user_account_index_email ON user_account(email);
CREATE INDEX user_account_index_username ON user_account(username);
CREATE INDEX user_account_index_locked ON user_account(locked);
CREATE INDEX user_account_index_active ON user_account(active);

INSERT INTO user_account (file_as, username, email, password, password_expiry_date) VALUES ('Digify Bot', 'digifybot', 'digifybot@gmail.com', 'Lu%2Be%2BRZfTv%2F3T0GR%2Fwes8QPJvE3Etx1p7tmryi74LNk%3D', '2025-12-31');
INSERT INTO user_account (file_as, username, email, password, password_expiry_date) VALUES ('Lawrence Agulto', 'ldagulto', 'lawrenceagulto.317@gmail.com', 'Lu%2Be%2BRZfTv%2F3T0GR%2Fwes8QPJvE3Etx1p7tmryi74LNk%3D', '2025-12-31');

DROP TABLE IF EXISTS password_history;

CREATE TABLE password_history (
    password_history_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    user_account_id CHAR(36) NOT NULL,
    password VARCHAR(255) NOT NULL,
    password_change_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    changed_by CHAR(36) NOT NULL,
    FOREIGN KEY (user_account_id) REFERENCES user_account(user_account_id),
    FOREIGN KEY (changed_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX password_history_index_user_account_id ON password_history(user_account_id);
CREATE INDEX password_history_index_changed_by ON password_history(changed_by);
CREATE INDEX password_history_index_password_change_date ON password_history(password_change_date);