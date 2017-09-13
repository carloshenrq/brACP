ALTER TABLE bracp_profile
	CHANGE COLUMN AvatarURL AvatarURL MEDIUMTEXT NULL DEFAULT NULL;

-- Tabela para correlação entre contas e perfils.
DROP TABLE IF EXISTS `bracp_profile_accounts`;
CREATE TABLE `bracp_profile_accounts` (

    `ProfileAccID`      INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `ProfileID`         INTEGER NOT NULL,
    `AccountID`         INTEGER NOT NULL,
    `AccountUserID`     VARCHAR(50) NOT NULL,
    `AccountSex`        ENUM('M', 'F') NOT NULL,
    `AccountVerifyDt`   DATETIME NOT NULL,

    CONSTRAINT `bracp_profile_accounts_f01` FOREIGN KEY (`ProfileID`) REFERENCES `bracp_profile` (`ProfileID`),
	UNIQUE INDEX `bracp_profile_accounts_u01` (`AccountID`)

) ENGINE=InnoDB
COLLATE='utf8_swedish_ci';

