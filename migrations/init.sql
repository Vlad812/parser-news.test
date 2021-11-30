#-------  DATABASE
DROP DATABASE IF EXISTS parserNews;
CREATE DATABASE IF NOT EXISTS `parserNews` CHARACTER SET utf8 COLLATE utf8_general_ci;
USE parserNews;

DROP TABLE IF EXISTS NewsResourceReference;
CREATE TABLE IF NOT EXISTS NewsResourceReference
(
    id      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    name    VARCHAR(250) NOT NULL,
    PRIMARY KEY (id)
)
    ENGINE = InnoDB
    CHARACTER SET utf8
    COLLATE utf8_general_ci
    COMMENT = 'Список новостных ресурсов для парсинга';

# ---
DROP TABLE IF EXISTS News;
CREATE TABLE IF NOT EXISTS News
(
    id         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    title      VARCHAR(250) DEFAULT NULL COMMENT 'Заголовок Статьи',
    link       VARCHAR(250) DEFAULT NULL COMMENT 'Ссылка на источник',
    article    TEXT DEFAULT NULL COMMENT 'Контент статьи',
    img        VARCHAR(250) DEFAULT NULL COMMENT 'Имя главной картинки',
    resourceId INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_News_NewsResourceReference FOREIGN KEY (`resourceId`) REFERENCES NewsResourceReference (`id`)
)
    ENGINE = InnoDB
    CHARACTER SET utf8
    COLLATE utf8_general_ci
    COMMENT = '';

INSERT INTO NewsResourceReference (`name`) VALUE ('RBC');
INSERT INTO NewsResourceReference (`name`) VALUE ('SomeNews');