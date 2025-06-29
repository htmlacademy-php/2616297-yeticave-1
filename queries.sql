-- Получить все категории
SELECT name, slug
FROM categories;

-- Получить самые новые, открытые лоты. Включает название, стартовую цену, ссылку на изображение, цену, название категории
SELECT l.id,
       l.name,
       l.start_price,
       MAX(b.buy_price) AS current_price,
       l.img_url,
       l.end_date,
       l.user_id,
       c.name           AS category_name,
       l.created_at
FROM lots l
         JOIN categories c on c.id = l.category_id
         LEFT JOIN buy_orders b on l.id = b.lot_id
WHERE l.winner_id IS NULL
  AND l.end_date > NOW()
GROUP BY l.id, l.created_at
ORDER BY l.created_at DESC;

-- Показать лот по его ID и название категории, к которой принадлежит лот
SELECT l.name,
       l.description,
       l.img_url,
       l.start_price,
       l.end_date,
       l.betting_step,
       l.user_id,
       l.winner_id,
       c.name AS category_name
FROM lots l
         JOIN categories c on c.id = l.category_id
WHERE l.id = 1;

-- Обновить название лота по его идентификатору
UPDATE lots
SET name = 'Новое имя'
WHERE id = 1;

-- Получить список ставок для лота по его идентификатору с сортировкой по дате
SELECT buy_price, lot_id, created_at
FROM buy_orders
WHERE lot_id = 3
ORDER BY created_at DESC;