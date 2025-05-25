<?php

declare(strict_types=1);

/**
 * @var string[] $categories_list Список категорий
 * @var array<int,array{name: string, category: string, price: int, img: string, end_date: string} $lots_list Список лотов
 */
?>
<main class="container">
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <?php foreach ($categories_list as $category): ?>
                <li class="promo__item promo__item--boards">
                    <a class="promo__link" href="pages/all-lots.html"><?= $category; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <?php foreach ($lots_list as $lot): ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="<?= $lot['img'] ?? '' ?>"
                             width="350"
                             height="260"
                             alt="<?= htmlspecialchars($lot['name'] ?? ''); ?>"
                        >
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?= $lot['category'] ?? '' ?></span>
                        <h3 class="lot__title">
                            <a class="text-link" href="pages/lot.html">
                                <?= htmlspecialchars($lot['name'] ?? ''); ?>
                            </a>
                        </h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount">Стартовая цена</span>
                                <span class="lot__cost"><?= format_price($lot['price'] ?? 0); ?></span>
                            </div>
                            <?php
                            if (isset($lot['end_date'])):
                                [$hours, $minutes] = get_dt_range($lot['end_date']);
                            ?>
                            <div class="lot__timer timer <?= $hours === 0 ? 'timer--finishing' : ''; ?>">
                                <?= "$hours: $minutes"; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</main>