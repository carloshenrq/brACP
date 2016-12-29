CREATE TABLE blacklist (
    AddressID   INTEGER      PRIMARY KEY AUTOINCREMENT
                             NOT NULL,
    Address     VARCHAR (15) DEFAULT ('0.0.0.0') 
                             NOT NULL,
    Reason      STRING       NOT NULL,
    TimeBlocked INTEGER      DEFAULT (0) NOT NULL,
    TimeExpire  INTEGER      DEFAULT (0) NOT NULL,
    Permanent   BOOLEAN      DEFAULT false
                             NOT NULL,
    RuleID      INTEGER      NULL
);

CREATE INDEX blacklist_i01 on blacklist (
    Address,
    TimeExpire,
    Permanent
);

CREATE INDEX blacklist_i02 on blacklist (
    RuleID
);

CREATE TABLE request (
    RequestID   INTEGER      PRIMARY KEY AUTOINCREMENT,
    Address     VARCHAR (15)  DEFAULT ('0.0.0.0') 
                              NOT NULL,
    UserAgent   VARCHAR (200) NOT NULL,
    RequestTime INTEGER       DEFAULT (0) 
                              NOT NULL,
    ServerTime  DECIMAL (16, 4) DEFAULT (0) 
                              NOT NULL,
    GMT         VARCHAR (50) NOT NULL,
    Method      VARCHAR (10)  NOT NULL,
    Scheme      VARCHAR (20)  NOT NULL,
    URI         VARCHAR (200) NOT NULL,
    Filename    VARCHAR (200) NOT NULL,
    PHPSession  VARCHAR (200) NOT NULL,
    GET         STRING  NOT NULL,
    POST        STRING  NOT NULL,
    SESSION     STRING  NOT NULL,
    UseToBan    BOOLEAN DEFAULT false NOT NULL
);

CREATE INDEX request_i01 ON request (
    Address,
    ServerTime,
    UseToBan
);

CREATE TABLE ip_data (
    LogID           INTEGER       PRIMARY KEY AUTOINCREMENT
                            NOT NULL,
    Address         VARCHAR (15)  NOT NULL
                            DEFAULT ('000.000.000.000'),
    Hostname        VARCHAR (100) NOT NULL,
    City            VARCHAR (100) NOT NULL,
    Region          VARCHAR (100) NOT NULL,
    Country         VARCHAR (10)  NOT NULL,
    Location        VARCHAR (100) NOT NULL,
    Origin          VARCHAR (200) NOT NULL,
    ServerTime      INTEGER DEFAULT(0) NOT NULL,
    GMT             VARCHAR (50) NOT NULL
);

CREATE INDEX ip_data_i01 ON ip_data (
    Address
);

CREATE TABLE rules (
    RuleID      INTEGER         PRIMARY KEY AUTOINCREMENT NOT NULL,
    Type        INTEGER         DEFAULT(0) NOT NULL,
    Enabled     BOOLEAN DEFAULT false NOT NULL,
    Rule        VARCHAR(200)    DEFAULT('') NOT NULL
);

CREATE INDEX rules_i01 on rules (
    Enabled,
    Type,
    Rule
);

CREATE TABLE safelist (
    Address VARCHAR(15)     NOT NULL,
    ServerTime      INTEGER NOT NULL,
    ExpireTime      INTEGER NOT NULL
);

CREATE INDEX safelist_i01 on safelist (
    Address,
    ExpireTime
);
