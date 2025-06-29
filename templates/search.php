<?php

declare(strict_types=1);

/**
 * @var string $categories_header HTML-представление категорий в шапке
 * @var bool $is_auth Флаг авторизации
 * @var string $lots Список лотов в HTML представлении
 * @var string $search_query Строка запроса
 */
?>
<main>
    <?= $categories_header; ?>
    <div class="container">
        <section class="lots">
            <h2>Результаты поиска по запросу «<span><?= $search_query; ?></span>»</h2>
            <?= empty($lots) ? 'Ничего не найдено по вашему запросу' : $lots; ?>
        </section>
        <?php if (!empty($pager['pages'])): ?>
        <ul class="pagination-list">
            <li class="pagination-item pagination-item-prev">
                <a
                    <?php if ($pager['prev'] !== null): ?>
                        href="<?= change_get_parameter(['page' => $pager['prev']]); ?>"
                    <?php endif; ?>
                >
                    Назад
                </a>
            </li>
            <?php foreach ($pager['pages'] as $number => $info): ?>
            <li class="pagination-item <?= $info['current'] === true ? 'pagination-item-active' : ''; ?>">
                <a href="<?= change_get_parameter(['page' => $number]); ?>"><?= $number; ?></a>
            </li>
            <?php endforeach; ?>
            <li class="pagination-item pagination-item-next">
                <a
                    <?php if ($pager['next'] !== null): ?>
                        href="<?= change_get_parameter(['page' => $pager['next']]); ?>"
                    <?php endif; ?>
                >
                    Вперед
                </a>
            </li>
        </ul>
        <?php endif; ?>
    </div>
</main>