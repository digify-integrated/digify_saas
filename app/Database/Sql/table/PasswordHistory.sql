DROP TABLE IF EXISTS password_history;

CREATE TABLE password_history (
    password_history_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    user_account_id INT UNSIGNED NOT NULL,
    password VARCHAR(255) NOT NULL,
    password_change_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    changed_by INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_account_id) REFERENCES user_accounts(user_account_id),
    FOREIGN KEY (changed_by) REFERENCES user_accounts(user_account_id)
);

CREATE INDEX password_history_idx_user_account_id ON password_history(user_account_id);
CREATE INDEX password_history_idx_changed_by ON password_history(changed_by);
CREATE INDEX password_history_idx_password_change_date ON password_history(password_change_date);