DROP DATABASE IF EXISTS scraper;
CREATE DATABASE scraper COLLATE utf8_general_ci;

USE scraper;

DROP TABLE IF EXISTS keywords;
CREATE TABLE keywords (
    id INT AUTO_INCREMENT,
    keyword VARCHAR(255) NOT NULL,
    UNIQUE (keyword),
    PRIMARY KEY (id)
);

DROP TABLE IF EXISTS rankcheck;
CREATE TABLE rankcheck (
    keyId INT NOT NULL,
    url VARCHAR(255),
    domain VARCHAR(255),
    position VARCHAR(255),
    page INT,
    rank INT,
    date DATE,
    searchId INT,
    FOREIGN KEY (keyId) REFERENCES keywords (id)
);

DROP TABLE IF EXISTS rankcrawl;
CREATE TABLE rankcrawl (
    keyword VARCHAR(255) NOT NULL,
    url VARCHAR(255),
    domain VARCHAR(255),
    position VARCHAR(255),
    page INT,
    rank INT,
    search VARCHAR(255)
);
