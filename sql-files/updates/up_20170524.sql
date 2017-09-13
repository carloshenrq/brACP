
DROP TABLE IF EXISTS `bracp_profile_report`;
CREATE TABLE `bracp_profile_report` (

    `ReportID` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `ProfileID` INTEGER NOT NULL,
    `InformerID` INTEGER NOT NULL,
    `ReportDate` DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',

    -- Tipos para reportar o perfil de algum outro usuário
    -- 'F' = Ofensivo
    -- 'S' = Conteúdo sexual
    -- 'B' = Bullying
    -- 'O' = Outros
    `ReportType` ENUM('F', 'S', 'B', 'O') NOT NULL DEFAULT 'O',
    `ReportText` TEXT NOT NULL,

    -- Dados para a resposta da denuncia
    `StaffID` INTEGER NULL DEFAULT NULL,
    `StaffReply` TEXT NULL DEFAULT NULL,
    `StaffReplyDate` DATETIME NULL DEFAULT NULL,
    -- 'A' = Aceito
    -- 'R' = Recusado
    `StaffStatus`   ENUM('A', 'R') NULL DEFAULT NULL,

    CONSTRAINT `bracp_profile_report_f01` FOREIGN KEY (`ProfileID`) REFERENCES `bracp_profile` (`ProfileID`),
    CONSTRAINT `bracp_profile_report_f02` FOREIGN KEY (`InformerID`) REFERENCES `bracp_profile` (`ProfileID`),
    CONSTRAINT `bracp_profile_report_f03` FOREIGN KEY (`StaffID`) REFERENCES `bracp_profile` (`ProfileID`)

) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

ALTER TABLE bracp_profile
    ADD COLUMN `CanReportProfiles` BOOLEAN NOT NULL DEFAULT true AFTER `CanCreateAccount`;

DROP TABLE IF EXISTS `bracp_profile_block`;
CREATE TABLE `bracp_profile_block` (
    `BlockID`   INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `ProfileID` INTEGER NOT NULL,
    `BlockedID` INTEGER NOT NULL,
    `BlockedDate` DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',

    CONSTRAINT `bracp_profile_block_f01` FOREIGN KEY (`ProfileID`) REFERENCES `bracp_profile` (`ProfileID`),
    CONSTRAINT `bracp_profile_block_f02` FOREIGN KEY (`BlockedID`) REFERENCES `bracp_profile` (`ProfileID`),
    UNIQUE INDEX `bracp_profile_block_u01` (`ProfileID`, `BlockedID`)

) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

