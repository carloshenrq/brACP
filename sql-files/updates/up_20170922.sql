-- Adicionado coluna para servidores
ALTER TABLE bracp_profile_accounts
    ADD COLUMN ServerID INTEGER NOT NULL DEFAULT 1,
    ADD CONSTRAINT `bracp_profile_accounts_f02` FOREIGN KEY (`ServerID`) REFERENCES `bracp_servers` (`ServerID`);
