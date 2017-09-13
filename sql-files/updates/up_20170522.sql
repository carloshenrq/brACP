ALTER TABLE bracp_profile
    ADD COLUMN `Visibility`        ENUM('P', 'F', 'M') NOT NULL DEFAULT 'M';