ALTER TABLE bracp_profile
    ADD COLUMN RegisterDate DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00' AFTER Verified;

UPDATE bracp_profile SET RegisterDate = NOW();

ALTER TABLE bracp_profile
    ADD COLUMN `ShowBirthdate` ENUM('P', 'F', 'M') NOT NULL DEFAULT 'M';

ALTER TABLE bracp_profile
    ADD COLUMN `ShowEmail` ENUM('P', 'F', 'M') NOT NULL DEFAULT 'M';

ALTER TABLE bracp_profile
    ADD COLUMN `ShowFacebook` ENUM('P', 'F', 'M') NOT NULL DEFAULT 'M';

ALTER TABLE bracp_profile
    ADD COLUMN `AllowMessage` ENUM('P', 'F', 'M') NOT NULL DEFAULT 'M';
