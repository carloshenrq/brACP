-- Tabela para mensagens de anuncios e avisos do sistema.
-- É uma tabela de avisos globais, não é especifica por usuário.
DROP TABLE IF EXISTS `bracp_announces`;
CREATE TABLE `bracp_announces` (
    `AnnounceID`            INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `AnnounceTitle`         VARCHAR(60) NULL DEFAULT NULL,
    `AnnounceContent`       TEXT NOT NULL,
    `AnnounceCreateDt`      DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',
    `AnnounceShowDt`        DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',
    `AnnounceEndDt`         DATETIME NULL DEFAULT NULL,
    -- 'I' : Info (lightblue)
    -- 'W' : Warning (orange/yellow)
    -- 'E' : Error (red)
    `AnnounceType`          ENUM('I', 'W', 'E') NOT NULL DEFAULT 'I',
    -- 'N' : Normal announce, topscreen without user prompt to confirm.
    -- 'A' : Agree/Refuse, modalbox... if refused, the user'll be logged out. (Only logged user)
    -- 'O' : OK, modalbox, just need to click in 'OK' to close. (Only logged user)
    `AnnounceShowType`      ENUM('N', 'A', 'O') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

-- Tabela para confirmações de mensagens e avisos dados pelo sistema
-- Que o usuário aceitou. (somente usuários logados)
DROP TABLE IF EXISTS `bracp_announces_profiles`;
CREATE TABLE `bracp_announces_profiles` (
    `AnnounceID`    INTEGER NOT NULL,
    `ProfileID`     INTEGER NOT NULL,
    -- 'A': Accepted
    -- 'O': Ok, dont show this again
    `ResponseType`  ENUM('A', 'O') NOT NULL,
    `ResponseDt`    DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',

    CONSTRAINT `bracp_announces_profiles_f01` FOREIGN KEY (`AnnounceID`) REFERENCES `bracp_announces` (`AnnounceID`),
    CONSTRAINT `bracp_announces_profiles_f02` FOREIGN KEY (`ProfileID`) REFERENCES `bracp_profile` (`ProfileID`)
) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

-- Tabela de manutenções. Ao lançar, será gerado anuncios do tipo global tipo 'W'-'N'
-- O Aviso começará a ser exibido, 3 dias antes do periodo de manutenção.
DROP TABLE IF EXISTS `bracp_maintences`;
CREATE TABLE `bracp_maintences` (
    `MaintenceID`       INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `MaintenceStartDt`  DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',
    `MaintenceEndDt`    DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',
    `AnnounceID`        INTEGER NULL DEFAULT NULL,

    CONSTRAINT `bracp_maintences_f01` FOREIGN KEY (`AnnounceID`) REFERENCES `bracp_announces`(`AnnounceID`)
) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';
