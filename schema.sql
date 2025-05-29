DROP
    DATABASE IF EXISTS yeticave;

CREATE
    DATABASE yeticave
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE
    yeticave;

CREATE TABLE categories
(
    id             INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name           VARCHAR(128) NOT NULL,
    character_code VARCHAR(128) NOT NULL COMMENT 'Символьный код категории латиницей',
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = INNODB;

CREATE TABLE users
(
    id            INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    email         VARCHAR(128) NOT NULL UNIQUE,
    first_name    VARCHAR(128) NOT NULL,
    password_hash CHAR(60)     NOT NULL,
    contact_info  VARCHAR(256) COMMENT 'Контактная информация для связи с пользователем',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = INNODB;

CREATE TABLE lots
(
    id           INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name         VARCHAR(128) NOT NULL,
    description  VARCHAR(512) NOT NULL,
    img_url      VARCHAR(128) NOT NULL,
    start_price  INT UNSIGNED NOT NULL,
    end_date     TIMESTAMP    NOT NULL,
    betting_step INT UNSIGNED NOT NULL COMMENT 'Шаг ставки лота',
    user_id      INT UNSIGNED NOT NULL,
    winner_id    INT UNSIGNED,
    category_id  INT UNSIGNED NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (winner_id) REFERENCES users (id),
    FOREIGN KEY (category_id) REFERENCES categories (id),
    FULLTEXT (name, description)
) ENGINE = INNODB;

CREATE TABLE buy_orders
(
    id         INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    buy_price  INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    lot_id     INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (lot_id) REFERENCES lots (id)
) ENGINE = INNODB;