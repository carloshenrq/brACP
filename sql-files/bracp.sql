--    ________________________________________________________
--   /                                                        \
--   |       _           _   _   _                            |
--   |      | |__  _ __ / \ | |_| |__   ___ _ __   __ _       |
--   |      | '_ \| '__/ _ \| __| '_ \ / _ \ '_ \ / _` |      |
--   |      | |_) | | / ___ \ |_| | | |  __/ | | | (_| |      |
--   |      |_.__/|_|/_/   \_\__|_| |_|\___|_| |_|\__,_|      |
--   |                                                        |
--   |                    brAthena Script                     |
--   |--------------------------------------------------------|
--   | Nome do Script: bracp.sql                              |
--   |--------------------------------------------------------|
--   | Criado por: CarlosHenrq [brAthena]                     |
--   |--------------------------------------------------------|
--   | Descrição: Script de instalação para o banco de dados  |
--   |            do brACP.                                   |
--   |--------------------------------------------------------|
--   | Changelog:                                             |
--   | 1.0 - Criação do script de instalação do banco.        |
--   |--------------------------------------------------------|
--   | - Anotações                                            |
--   |   * Cuidado ao rodar este script uma segunda vez       |
--   |     ele pode apagar seus dados das tabelas e você      |
--   |     ficar sem ter como recuperar as informações.       |
--   \________________________________________________________/

SET foreign_key_checks = 0;

-- Tabela de informações para os profiles
DROP TABLE IF EXISTS `bracp_profile`;
CREATE TABLE `bracp_profile` (

    `ProfileID`         INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `Name`              VARCHAR(256) NOT NULL DEFAULT '',
    `Gender`            ENUM('M', 'F', 'O') NOT NULL DEFAULT 'O',
    `Birthdate`         DATE NOT NULL DEFAULT '1001-01-01',
    `Email`             VARCHAR(60) NULL DEFAULT NULL,
    `Password`          VARCHAR(128) NULL DEFAULT NULL,
    `AvatarURL`         MEDIUMTEXT NULL DEFAULT NULL,
    `AboutMe`           TEXT NULL DEFAULT NULL,
    `CanCreateAccount`  BOOLEAN NOT NULL DEFAULT true,
    `CanReportProfiles` BOOLEAN NOT NULL DEFAULT true,
    `Blocked`           BOOLEAN NOT NULL DEFAULT false,
    `BlockedReason`     VARCHAR(2048) NULL DEFAULT NULL,
    `BlockedUntil`      INTEGER NULL DEFAULT NULL,
    `Verified`          BOOLEAN NOT NULL DEFAULT false,
    `RegisterDate`      DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',

    -- Informações de vinculo com facebook.
    `FacebookID`        VARCHAR(30) NULL DEFAULT NULL,

    -- Algumas configurações para o perfil do usuário
    -- 'P' => Público, qualquer um pode visualizar o perfil.
    -- 'F' => Amigos, somente amigos deste perfil podem visualizar o conteúdo. (Usuários logados podem adicionar somente)
    -- 'M' => Apenas eu, ninguém pode visualizar os dados deste perfil. (Usuários logados podem adicionar somente)
    `Visibility`        ENUM('P', 'F', 'M') NOT NULL DEFAULT 'M',
    `ShowBirthdate`     ENUM('P', 'F', 'M') NOT NULL DEFAULT 'M',
    `ShowEmail`         ENUM('P', 'F', 'M') NOT NULL DEFAULT 'M',
    `ShowFacebook`      ENUM('P', 'F', 'M') NOT NULL DEFAULT 'M',
    `AllowMessage`      ENUM('P', 'F', 'M') NOT NULL DEFAULT 'M',

    -- Nivel de privilégios do perfil.
    -- 'U' : Usuário comum; User
    -- 'M' : Moderador; Moderator
    -- 'A' : Administrador; Administrator
    `Privileges`        ENUM('U', 'M', 'A') NOT NULL DEFAULT 'U',

    -- Informações para chave de autenticação 2 fatores do google
    -- GATimeValid = Inteiro representado no calculo:
    --               GATimeValid * 30 = Tempo de tolerância.
    `GAAllowed`         BOOLEAN NOT NULL DEFAULT FALSE,
    `GASecret`          VARCHAR(16) NULL DEFAULT NULL,

    -- Informações sobre chaves de profile.
    UNIQUE INDEX `bracp_profile_u01` (`Email`),
    UNIQUE INDEX `bracp_profile_u02` (`FacebookID`)

) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

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


-- Tabela de código de ativações para perfils.
-- Na verdade, é utilizada todas as vezes que um novo e-mail é adicionado a conta
-- E precisa ser verificado.
DROP TABLE IF EXISTS `bracp_profile_verify`;
CREATE TABLE `bracp_profile_verify` (

    `VerifyID`          INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `ProfileID`         INTEGER NOT NULL,
    `VerifyEmail`       VARCHAR(60) NOT NULL,
    `VerifyCode`        VARCHAR(32) NOT NULL,
    `VerifyProfile`     BOOLEAN NOT NULL DEFAULT false,
    `VerifyUsed`        BOOLEAN NOT NULL DEFAULT false,
    `VerifyUsedDt`      DATETIME NULL DEFAULT NULL,
    `VerifyExpireDt`    DATETIME NULL DEFAULT NULL,

    CONSTRAINT `bracp_profile_verify_f01` FOREIGN KEY (`ProfileID`) REFERENCES `bracp_profile` (`ProfileID`),
    UNIQUE INDEX `bracp_profile_verify_u01` (`VerifyCode`)

) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

-- Tabela para guardar informações de logs.
-- L: Login realizado com sucesso
-- W: Login incorreto
-- A: Administrativo (+ Incluir motivo)
-- O: Outros, exemplo: "Alteração de senha"
-- B: Bloqueio
DROP TABLE IF EXISTS `bracp_profile_logs`;
CREATE TABLE `bracp_profile_logs` (

    `LogID`         INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `ProfileID`     INTEGER NOT NULL,
    `LogType`       ENUM('L', 'W', 'A', 'O', 'B') NOT NULL,
    `LogMessage`    VARCHAR(512) NULL DEFAULT NULL,
    `LogDateTime`   DATETIME NOT NULL,
    `LogAddress`    VARCHAR(50) NOT NULL,

    CONSTRAINT `bracp_profile_logs_f01` FOREIGN KEY (`ProfileID`) REFERENCES `bracp_profile` (`ProfileID`)

) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

-- Tabela para guardar informações dos servidores do sistema.
DROP TABLE IF EXISTS `bracp_servers`;
CREATE TABLE `bracp_servers` (

    `ServerID`      INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `Server`        VARCHAR(20) NOT NULL,
    `ServerEnabled` BOOLEAN NOT NULL DEFAULT true,
    `SQLHost`       VARCHAR(256) NOT NULL DEFAULT '127.0.0.1',
    `SQLPort`       INTEGER NOT NULL DEFAULT 3306,
    `SQLUser`       VARCHAR(30) NOT NULL DEFAULT 'ragnarok',
    `SQLPass`       VARCHAR(256) NOT NULL DEFAULT 'ragnarok',
    `SQLDatabase`   VARCHAR(30) NOT NULL DEFAULT 'ragnarok',
    -- S: SQL-Master
    -- D: SQL-Dependant
    `SQLType`       ENUM('S', 'D') NOT NULL DEFAULT 'S',
    `LoginServer`   BOOLEAN NOT NULL DEFAULT true,
    `LoginIP`       VARCHAR(50) NOT NULL DEFAULT '127.0.0.1',
    `LoginPort`     INTEGER NOT NULL DEFAULT 6900,
    `CharIP`        VARCHAR(50) NOT NULL DEFAULT '127.0.0.1',
    `CharPort`      INTEGER NOT NULL DEFAULT 6121,
    `MapIP`         VARCHAR(50) NOT NULL DEFAULT '127.0.0.1',
    `MapPort`       INTEGER NOT NULL DEFAULT 5121,

    INDEX `bracp_servers_i01` (`ServerEnabled`, `SQLType`, `LoginServer`)

) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

INSERT INTO bracp_servers VALUES
	(NULL, 'Midgard', true, '127.0.0.1', 3306,
		'bracp', 'bracp', 'ragnarok', 'S',
		true,
		'127.0.0.1', 6900,
		'127.0.0.1', 6121,
		'127.0.0.1', 5121);

-- Tabela para guardar informações sobre o status online do servidor.
DROP TABLE IF EXISTS `bracp_servers_status`;
CREATE TABLE `bracp_servers_status` (

    `StatusID`      INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `ServerID`      INTEGER NOT NULL,
    `LoginServer`   BOOLEAN NOT NULL DEFAULT false,
    `LoginPing`     DECIMAL(5, 3) NOT NULL DEFAULT 0,
    `CharServer`    BOOLEAN NOT NULL DEFAULT false,
    `CharPing`      DECIMAL(5, 3) NOT NULL DEFAULT 0,
    `MapServer`     BOOLEAN NOT NULL DEFAULT false,
    `MapPing`       DECIMAL(5, 3) NOT NULL DEFAULT 0,
    `AveragePing`   DECIMAL(5, 3) NOT NULL DEFAULT 0,
    `StatusDate`    DATETIME NOT NULL,
    `StatusExpire`  DATETIME NOT NULL,

    CONSTRAINT `bracp_servers_status_f01` FOREIGN KEY (`ServerID`) REFERENCES `bracp_servers` (`ServerID`)

) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

-- Tabela para correlação entre contas e perfils.
DROP TABLE IF EXISTS `bracp_profile_accounts`;
CREATE TABLE `bracp_profile_accounts` (

    `ProfileAccID`      INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `ProfileID`         INTEGER NOT NULL,
    `AccountID`         INTEGER NOT NULL,
    `AccountUserID`     VARCHAR(50) NOT NULL,
    `AccountSex`        ENUM('M', 'F') NOT NULL,
    `AccountVerifyDt`   DATETIME NOT NULL,

    CONSTRAINT `bracp_profile_accounts_f01` FOREIGN KEY (`ProfileID`) REFERENCES `bracp_profile` (`ProfileID`)

) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

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
    -- 'A' : Agree/Refuse, modalbox... if refused, the user'll be logged out.
    -- 'O' : OK, modalbox, just need to click in 'OK' to close.
    `AnnounceShowType`      ENUM('N', 'A', 'O') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

-- Tabela para confirmações de mensagens e avisos dados pelo sistema
-- Que o usuário aceitou.
DROP TABLE IF EXISTS `bracp_announces_profiles`;
CREATE TABLE `bracp_announces_profiles` (
    `AnnounceID`    INTEGER NOT NULL,
    `ProfileID`     INTEGER NOT NULL,
    `ResponseType`  ENUM('A', 'O') NOT NULL,
    `ResponseDt`    DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',

    CONSTRAINT `bracp_announces_profiles_f01` FOREIGN KEY (`AnnounceID`) REFERENCES `bracp_announces` (`AnnounceID`),
    CONSTRAINT `bracp_announces_profiles_f02` FOREIGN KEY (`ProfileID`) REFERENCES `bracp_profile` (`ProfileID`)
) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

-- Tabela de manutenções. Ao lançar, será gerado anuncios do tipo global tipo 'W'-'N'
DROP TABLE IF EXISTS `bracp_maintences`;
CREATE TABLE `bracp_maintences` (
    `MaintenceID`       INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `MaintenceStartDt`  DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',
    `MaintenceEndDt`    DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',
    `AnnounceID`        INTEGER NULL DEFAULT NULL,

    CONSTRAINT `bracp_maintences_f01` FOREIGN KEY (`AnnounceID`) REFERENCES `bracp_announces`(`AnnounceID`)
) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

SET foreign_key_checks = 1;
