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
    `StatusTime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `StatusExpire` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `PlayerCount` INTEGER NOT NULL DEFAULT 0,
    INDEX (`ServerIndex`, `StatusExpire`)
) ENGINE=MyISAM COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_donations_promo`;
CREATE TABLE IF NOT EXISTS `bracp_donations_promo` (
    `PromotionID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `PromotionDescription` VARCHAR(1024) NOT NULL DEFAULT '',
    `BonusMultiply` INTEGER NOT NULL DEFAULT 0,
    `PromotionStartDate` DATE NOT NULL DEFAULT '0000-00-00',
    `PromotionEndDate` DATE NOT NULL DEFAULT '0000-00-00',
    `PromotionCanceled` BOOLEAN NOT NULL DEFAULT FALSE,
    INDEX (`PromotionStartDate`, `PromotionEndDate`)
) ENGINE=InnoDB COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_donations`;
CREATE TABLE IF NOT EXISTS `bracp_donations` (
    `DonationID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `PromotionID` INTEGER NULL DEFAULT NULL,
    `ReceiverID` VARCHAR(50) NULL DEFAULT NULL,
    `ReceiverMail` VARCHAR(100) NULL DEFAULT NULL,
    `SandboxMode` BOOLEAN NOT NULL DEFAULT FALSE,
    `TransactionDrive` VARCHAR(20) NOT NULL DEFAULT 'PAYPAL',
    `TransactionCode` VARCHAR(100) NOT NULL DEFAULT '',
    `TransactionType` VARCHAR(50) NULL DEFAULT NULL,
    `TransactionUserID` VARCHAR(23) NULL DEFAULT NULL,
    `PayerID` VARCHAR(50) NULL DEFAULT NULL,
    `PayerMail` VARCHAR(100) NULL DEFAULT NULL,
    `PayerStatus` VARCHAR(30) NULL DEFAULT NULL,
    `PayerName` VARCHAR(100) NULL DEFAULT NULL,
    `PayerCountry` VARCHAR(50) NULL DEFAULT NULL,
    `PayerState` VARCHAR(50) NULL DEFAULT NULL,
    `PayerCity` VARCHAR(50) NULL DEFAULT NULL,
    `PayerAddress` VARCHAR(200) NULL DEFAULT NULL,
    `PayerZipCode` VARCHAR(30) NULL DEFAULT NULL,
    `PayerAddressConfirmed` BOOLEAN NULL DEFAULT NULL,
    `DonationValue` DECIMAL(12, 2) NULL DEFAULT NULL,
    `DonationPayment` DATETIME NULL DEFAULT NULL,
    `DonationStatus` VARCHAR(30) NULL DEFAULT NULL,
    `DonationType` VARCHAR(30) NULL DEFAULT NULL,
    `VerifySign` TEXT NULL DEFAULT NULL,
    FOREIGN KEY (`PromotionID`) REFERENCES `bracp_donations_promo` (`PromotionID`),
    UNIQUE INDEX (`TransactionDrive`, `TransactionCode`)
) ENGINE=InnoDB COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_compensations`;
CREATE TABLE IF NOT EXISTS `bracp_compensations` (
    `CompensateID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `DonationID` INTEGER NOT NULL,
    `AccountID` INTEGER NULL DEFAULT NULL,
    `UserID` VARCHAR(23) NULL DEFAULT NULL,
    `CompensatePending` BOOLEAN NOT NULL DEFAULT TRUE,
    `CompensateDate` DATETIME NULL DEFAULT NULL,

    FOREIGN KEY (`DonationID`) REFERENCES `bracp_donations` (`DonationID`)
) ENGINE=InnoDB COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_recover`;
CREATE TABLE IF NOT EXISTS `bracp_recover` (
    `RecoverID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `AccountID` INTEGER NOT NULL,
    `RecoverCode` VARCHAR(32) NOT NULL,
    `RecoverDate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `RecoverExpire` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
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
    `EmailLogDate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00', 
    INDEX (`AccountID`)
) ENGINE=InnoDB COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_themes`;
CREATE TABLE IF NOT EXISTS `bracp_themes` (
    `ThemeID` INTEGER NOT NULL PRIMARY KEY,
    `Name` VARCHAR(20) NOT NULL DEFAULT '',
    `Version` VARCHAR(10) NOT NULL DEFAULT '',
    `Folder` VARCHAR(100) NOT NULL DEFAULT '',
    `ImportTime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00', 
    UNIQUE INDEX (`Folder`)
) ENGINE=MyISAM COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_account_confirm`;
CREATE TABLE IF NOT EXISTS `bracp_account_confirm` (
    `ConfirmationID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `AccountID` INTEGER NOT NULL,
    `ConfirmationCode` VARCHAR(32) NOT NULL,
    `ConfirmationDate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ConfirmationExpire` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ConfirmationUsed` BOOLEAN NOT NULL DEFAULT FALSE,
    UNIQUE INDEX (`ConfirmationCode`),
    INDEX (`AccountID`)
) ENGINE=MyISAM COLLATE='utf8_swedish_ci';

SET FOREIGN_KEY_CHECKS = 1;
