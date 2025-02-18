DROP TABLE IF EXISTS password_reset_tokens;

CREATE TABLE password_reset_tokens (
    password_reset_token_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    user_account_id INT UNSIGNED NOT NULL,
    reset_token VARCHAR(255),
    reset_token_expiry_date DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    FOREIGN KEY (user_account_id) REFERENCES user_accounts(user_account_id)
);

CREATE INDEX password_reset_tokens_idx_reset_token ON password_reset_tokens(reset_token);
CREATE INDEX password_reset_tokens_idx_reset_token_expiry_date ON password_reset_tokens(reset_token_expiry_date);