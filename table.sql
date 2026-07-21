CREATE TABLE ccp_cache_request (
    id int(10) NOT NULL,
    idTile varchar(255) NOT NULL,
    eventDate datetime NOT NULL DEFAULT current_timestamp()
);
 CREATE TABLE ccp_cache_request_sto (
    id int(10) NOT NULL DEFAULT 0,
    idTile varchar(255) NOT NULL,
    eventDate datetime NOT NULL DEFAULT current_timestamp()
);
 CREATE TABLE ccp_osm_request (
    id int(10) NOT NULL,
    idTile varchar(255) NOT NULL,
    eventDate datetime NOT NULL DEFAULT current_timestamp()
);

CREATE TABLE ccp_events (
    id INT(10) NOT NULL AUTO_INCREMENT,
    url VARCHAR(1024) NOT NULL,
    eventType VARCHAR(255) NOT NULL,
    eventDate DATETIME NOT NULL,
    PRIMARY KEY (id)
);

ALTER TABLE
    ccp_events
ADD
    PRIMARY KEY (id);

ALTER TABLE
    ccp_cache_request
ADD
    PRIMARY KEY (id);

ALTER TABLE
    ccp_cache_request_sto
ADD
    PRIMARY KEY (id);

ALTER TABLE
    ccp_osm_request
ADD
    PRIMARY KEY (id);

ALTER TABLE
    ccp_cache_request
MODIFY
    id int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE
    ccp_osm_request
MODIFY
    id int(10) NOT NULL AUTO_INCREMENT;