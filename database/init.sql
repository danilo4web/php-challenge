CREATE DATABASE IF NOT EXISTS `stock_db`;

CREATE TABLE `stock_db`.`users` (
    `id`       int(11) NOT NULL AUTO_INCREMENT,
    `name`     varchar(100) NOT NULL,
    `email`    varchar(50)  NOT NULL UNIQUE,
    `password` varchar(128),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `stock_db`.`stock_market_quotes` (
    `id`      int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `json_response`  json     NOT NULL,
    `date`    datetime NOT NULL,
    PRIMARY KEY (`id`),
    INDEX (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES users(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
