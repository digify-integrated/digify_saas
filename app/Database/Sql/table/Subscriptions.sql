DROP TABLE IF EXISTS subscriptions;

CREATE TABLE subscriptions (
    subscription_id UNSIGNED INT AUTO_INCREMENT PRIMARY KEY,
    subscription_code VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX subscriptions_idx_subscription_code ON subscriptions(subscription_code);
