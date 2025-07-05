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
    id         INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name       VARCHAR(128) NOT NULL,
    slug       VARCHAR(128) NOT NULL COMMENT 'Символьный код категории латиницей',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = INNODB;

CREATE TABLE users
(
    id            INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    email         VARCHAR(128) NOT NULL UNIQUE,
    first_name    VARCHAR(128) NOT NULL,
    password_hash CHAR(60)     NOT NULL,
    contact_info  VARCHAR(256) NOT NULL COMMENT 'Контактная информация для связи с пользователем',
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
    win_email_sent BOOLEAN DEFAULT FALSE,
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

INSERT INTO categories (name, slug)
VALUES ('Доски и лыжи', 'boards'),
       ('Крепления', 'attachment'),
       ('Ботинки', 'boots'),
       ('Одежда', 'clothing'),
       ('Инструменты', 'tools'),
       ('Разное', 'other');

INSERT INTO users (email, first_name, password_hash, contact_info)
VALUES ('test@test.com', 'Артём', '$2y$12$4Umg0rCJwMswRw/l.SwHvuQV01coP0eWmGzd61QH2RvAOMANUBGC.', ''),
       ('unique@test.com', 'Вероника', '$2y$13$xeDfQumlmdm0Sco.4qmH1OGfUUmOcuRmfae0dPJhjX1Bq0yYhqbNi', '');

INSERT INTO lots (name, description, img_url, start_price, end_date, betting_step, user_id, category_id)
VALUES ('2014 Rossignol District Snowboard', '', '/img/lot-1.jpg', 10999, DATE_ADD(NOW(), INTERVAL 7 DAY), 150, 1, 1),
       ('DC Ply Mens 2016/2017 Snowboard', '', '/img/lot-2.jpg', 159999, DATE_ADD(NOW(), INTERVAL 14 DAY), 300, 2, 1),
       ('Крепления Union Contact Pro 2015 года размер L/XL', '', '/img/lot-3.jpg', 8000,
        DATE_ADD(NOW(), INTERVAL 30 DAY),
        1000, 1, 2),
       ('Ботинки для сноуборда DC Mutiny Charocal', '', '/img/lot-4.jpg', 10999, DATE_ADD(NOW(), INTERVAL 5 DAY), 150,
        1, 3),
       ('Куртка для сноуборда DC Mutiny Charocal', '', '/img/lot-5.jpg', 7500, DATE_ADD(NOW(), INTERVAL -1 DAY), 300, 2,
        4),
       ('Маска Oakley Canopy', '', '/img/lot-6.jpg', 5400, DATE_ADD(NOW(), INTERVAL -7 DAY), 300, 2, 6);

INSERT INTO buy_orders (buy_price, user_id, lot_id)
VALUES (9000, 1, 3),
       (10000, 1, 3);