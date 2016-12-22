CREATE TABLE blacklist (
    AddressID   INTEGER      PRIMARY KEY AUTOINCREMENT
                             NOT NULL,
    Address     VARCHAR (15) DEFAULT ('0.0.0.0') 
                             NOT NULL,
    Reason      STRING       NOT NULL,
    TimeBlocked INTEGER      DEFAULT (0) NOT NULL,
    TimeExpire  INTEGER      DEFAULT (0) NOT NULL,
    Permanent   BOOLEAN      DEFAULT false
                             NOT NULL
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
    UseToBan    BOOLEAN DEFAULT false NOT NULL
);

CREATE INDEX request_i01 ON request (
    Address,
    ServerTime,
    UseToBan
);

CREATE TABLE ip_data (
    LogID     INTEGER       PRIMARY KEY AUTOINCREMENT
                            NOT NULL,
    IpAddress VARCHAR (15)  NOT NULL
                            DEFAULT ('000.000.000.000'),
    UserAgent VARCHAR (200) NOT NULL,
    Hostname  VARCHAR (100) NOT NULL,
    City      VARCHAR (100) NOT NULL,
    Region    VARCHAR (100) NOT NULL,
    Country   VARCHAR (10)  NOT NULL,
    Location  VARCHAR (100) NOT NULL,
    Origin    VARCHAR (200) NOT NULL,
    ServerTime  DECIMAL (16, 4) DEFAULT (0) 
                              NOT NULL,
    GMT         VARCHAR (50) NOT NULL
);

CREATE INDEX ip_data_i01 ON ip_data (
    IpAddress
);
