<?php

declare(strict_types=1);

/**
 * @var string[] $categories_list Список категорий
 * @var string $lots Список лотов в HTML представлении
 */
?>
<main class="container">
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <?php foreach ($categories_list as $category): ?>
                <li class="promo__item <?= isset($category['slug']) ? "promo__item--{$category['slug']}" : '' ?>">
                    <a class="promo__link" href="pages/all-lots.html"><?= $category['name'] ?? '' ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <?= $lots; ?>
    </section>
</main>