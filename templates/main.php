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
            <?php foreach ($categories_list as $category):
            $slug = htmlspecialchars($category['slug'] ?? '');
            $slug_class = !empty($slug) ? "promo__item--{$slug}" : '';
            ?>
                <li class="promo__item <?= $slug_class; ?>">
                    <a
                        class="promo__link"
                        href="/category.php?id=<?= $category['id'] ?? 0; ?>"
                    >
                        <?= htmlspecialchars($category['name'] ?? ''); ?>
                    </a>
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