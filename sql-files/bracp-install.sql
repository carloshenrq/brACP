-- ----------------------------------------------------------------------------- --
-- Tabelas de instalação para ambiente pag-seguro e log de informações
-- para o painel de controle.
-- ----------------------------------------------------------------------------- --

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `bracp_server_status`;
CREATE TABLE IF NOT EXISTS `bracp_server_status` (
    `StatusID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `ServerIndex` INTEGER NOT NULL,
    `ServerName` VARCHAR(100) NOT NULL,
    `MapStatus` BOOLEAN NOT NULL DEFAULT false,
    `CharStatus` BOOLEAN NOT NULL DEFAULT false,
    `LoginStatus` BOOLEAN NOT NULL DEFAULT false,
    `StatusTime` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
    `StatusExpire` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
    `PlayerCount` INTEGER NOT NULL DEFAULT 0,
    INDEX (`ServerIndex`, `StatusExpire`)
) ENGINE=MyISAM COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_recover`;
CREATE TABLE IF NOT EXISTS `bracp_recover` (
    `RecoverID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `AccountID` INTEGER NOT NULL,
    `RecoverCode` VARCHAR(32) NOT NULL,
    `RecoverDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
    `RecoverExpire` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
    `RecoverUsed` BOOLEAN NOT NULL DEFAULT FALSE,
    UNIQUE INDEX (`RecoverCode`),
    INDEX (`AccountID`)
) ENGINE=InnoDB COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_change_mail_log`;
CREATE TABLE IF NOT EXISTS `bracp_change_mail_log` (
    `EmailLogID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `AccountID` INTEGER NOT NULL,
    `EmailFrom` VARCHAR(39) NOT NULL,
    `EmailTo` VARCHAR(39) NOT NULL,
    `EmailLogDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00', 
    INDEX (`AccountID`)
) ENGINE=InnoDB COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_themes`;
CREATE TABLE IF NOT EXISTS `bracp_themes` (
    `ThemeID` INTEGER NOT NULL PRIMARY KEY,
    `Name` VARCHAR(20) NOT NULL DEFAULT '',
    `Version` VARCHAR(10) NOT NULL DEFAULT '',
    `Folder` VARCHAR(100) NOT NULL DEFAULT '',
    `ImportTime` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00', 
    UNIQUE INDEX (`Folder`)
) ENGINE=MyISAM COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_account_confirm`;
CREATE TABLE IF NOT EXISTS `bracp_account_confirm` (
    `ConfirmationID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `AccountID` INTEGER NOT NULL,
    `ConfirmationCode` VARCHAR(32) NOT NULL,
    `ConfirmationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
    `ConfirmationExpire` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
    `ConfirmationUsed` BOOLEAN NOT NULL DEFAULT FALSE,
    UNIQUE INDEX (`ConfirmationCode`),
    INDEX (`AccountID`)
) ENGINE=MyISAM COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_service_tokens`;
CREATE TABLE IF NOT EXISTS `bracp_service_tokens` (
    `TokenID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `Application` VARCHAR(100) NOT NULL DEFAULT '',
    `ApplicationHash` VARCHAR(32) NOT NULL,
    `Token` VARCHAR(32) NOT NULL,
    `DtCreated` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
    `DtExpire` DATETIME NULL DEFAULT NULL,
    `Enabled` BOOLEAN NOT NULL DEFAULT TRUE,

    UNIQUE INDEX (`Token`, `ApplicationHash`)
) ENGINE=MyISAM COLLATE='utf8_swedish_ci';

SET FOREIGN_KEY_CHECKS = 1;