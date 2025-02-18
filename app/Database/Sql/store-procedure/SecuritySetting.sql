DELIMITER //

DROP PROCEDURE IF EXISTS fetchSecuritySetting//

CREATE PROCEDURE fetchSecuritySetting(IN p_security_setting_id INT)
BEGIN
    IF EXISTS (SELECT 1 FROM security_settings WHERE security_setting_id = p_security_setting_id) THEN
        SELECT security_setting_name, value
        FROM security_settings
        WHERE security_setting_id = p_security_setting_id;
    END IF;
END //

DROP PROCEDURE IF EXISTS updateSecuritySetting//

CREATE PROCEDURE updateSecuritySetting(IN p_security_setting_id INT, IN p_value VARCHAR(1000), IN p_last_log_by INT)
BEGIN
    SET time_zone = '+08:00';

    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION, SQLWARNING
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF EXISTS (SELECT 1 FROM security_settings WHERE security_setting_id = p_security_setting_id) THEN
        UPDATE security_settings
        SET value = p_value
        WHERE security_setting_id = p_security_setting_id;

        COMMIT;
    ELSE
        ROLLBACK;
    END IF;
END //
