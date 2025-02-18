DROP TABLE IF EXISTS audit_log;

CREATE TABLE audit_log (
    audit_log_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    table_name VARCHAR(255) NOT NULL,
    reference_id INT UNSIGNED NOT NULL,
    log TEXT NOT NULL,
    changed_by INT UNSIGNED NOT NULL,
    changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (changed_by) REFERENCES user_accounts(user_account_id)
);

CREATE INDEX audit_log_idx_table_name ON audit_log(table_name, reference_id);
CREATE INDEX audit_log_idx_changed_at ON audit_log(changed_at);
CREATE INDEX audit_log_idx_changed_by ON audit_log(changed_by);