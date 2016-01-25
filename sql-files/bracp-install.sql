-- ----------------------------------------------------------------------------- --
-- Tabelas de instalação para ambiente pag-seguro e log de informações
-- para o painel de controle.
-- ----------------------------------------------------------------------------- --

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `bracp_donations_promo`;
CREATE TABLE IF NOT EXISTS `bracp_donations_promo` (
    `PromotionID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `PromotionDescription` VARCHAR(1024) NOT NULL DEFAULT '',
    `BonusMultiply` INTEGER NOT NULL DEFAULT 0,
    `PromotionStartDate` DATE NOT NULL DEFAULT '0000-00-00',
    `PromotionEndDate` DATE NOT NULL DEFAULT '0000-00-00',
    INDEX (`PromotionStartDate`, `PromotionEndDate`)
) ENGINE=InnoDB COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_donations`;
CREATE TABLE IF NOT EXISTS `bracp_donations` (
    `DonationID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `PromotionID` INTEGER NULL DEFAULT NULL,
    `DonationDate` DATE NOT NULL DEFAULT '0000-00-00',
    `DonationRefer` CHAR(32) NOT NULL,
    `DonationDrive` ENUM('PAGSEGURO') NOT NULL DEFAULT 'PAGSEGURO',
    `AccountID` INTEGER NOT NULL,
    `DonationValue` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `DonationBonus` INTEGER NOT NULL DEFAULT 0,
    `DonationTotalValue` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `CheckoutCode` VARCHAR(50) NULL DEFAULT NULL,
    `TransactionCode` VARCHAR(50) NULL DEFAULT NULL,
    `DonationReceiveBonus` BOOLEAN NOT NULL DEFAULT TRUE,
    `DonationCompensate` BOOLEAN NOT NULL DEFAULT FALSE,
    `DonationStatus` ENUM('INICIADA', 'PAGO', 'CANCELADO', 'ESTORNADO') NOT NULL DEFAULT 'INICIADA',
    `DonationPaymentDate` DATETIME NULL DEFAULT NULL,

    FOREIGN KEY (`PromotionID`) REFERENCES `bracp_donations_promo` (`PromotionID`)
) ENGINE=InnoDB COLLATE='utf8_swedish_ci';

DROP TABLE IF EXISTS `bracp_compensations`;
CREATE TABLE IF NOT EXISTS `bracp_compensations` (
    `CompensateID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `DonationID` INTEGER NOT NULL,
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

SET FOREIGN_KEY_CHECKS = 1;
