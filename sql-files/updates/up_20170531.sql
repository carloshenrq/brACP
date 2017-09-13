ALTER TABLE `bracp_profile`
    ADD COLUMN `Privileges` ENUM('U', 'M', 'A') NOT NULL DEFAULT 'U';