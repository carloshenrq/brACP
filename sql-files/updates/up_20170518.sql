-- Alterações para 2017-05-18
ALTER TABLE bracp_profile
    ADD COLUMN BlockedUntil INTEGER NULL DEFAULT NULL AFTER BlockedReason;

ALTER TABLE bracp_profile_logs
    CHANGE COLUMN LogType LogType ENUM('L', 'W', 'A', 'O', 'B') NOT NULL;
