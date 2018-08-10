DROP DATABASE IF EXISTS scraper;
CREATE DATABASE scraper COLLATE utf8_general_ci;

USE scraper;

DROP TABLE IF EXISTS rankcrawl;
CREATE TABLE rankcrawl (
    keyword VARCHAR(255) NOT NULL,
    url VARCHAR(255),
    domain VARCHAR(255),
    rank INT,
    date DATE
);
