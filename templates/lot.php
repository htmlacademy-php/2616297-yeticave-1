<?php

declare(strict_types=1);

/**
 * @var array{
 *     name: string,
 *     description: string,
 *     img_url: string,
 *     start_price: int,
 *     end_date: string,
 *     betting_step: int,
 *     category_id: int|null
 * } $lot Массив с информацией о конкретном лоте
 * @var string $categories_header HTML-представление категорий в шапке
 * @var array $bids Массив с информацией о ставках для лота
 * @var int $bids_total Количество ставок для лота
 * @var bool $is_authorized_to_place_bid Флаг проверки, что текущий пользователь может сделать ставку
 */
?>
<main>
    <?= $categories_header; ?>
    <section class="lot-item container">
        <h2><?= htmlspecialchars($lot['name'] ?? ''); ?></h2>
        <div class="lot-item__content">
            <div class="lot-item__left">
                <div class="lot-item__image">
                    <img
                        src="<?= $lot['img_url'] ?? ''; ?>"
                        width="730"
                        height="548"
                        alt="<?= htmlspecialchars($lot['name'] ?? ''); ?>"
                    >
                </div>
                <p class="lot-item__category">Категория: <span><?= $lot['category_name'] ?? ''; ?></span></p>
                <p class="lot-item__description">
                    <?= htmlspecialchars($lot['description'] ?? ''); ?>
                </p>
            </div>
            <div class="lot-item__right">
                <div class="lot-item__state">
                    <?php
                    if (isset($lot['end_date'])):
                        $remaining_time = get_dt_range($lot['end_date']);
                        $hours = $remaining_time['hours'] ?? 0;
                        ?>
                        <div class="lot-item__timer timer <?= $hours === 0 ? 'timer--finishing' : ''; ?>">
                            <?= format_dt_range($remaining_time); ?>
                        </div>
                    <?php endif; ?>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span class="lot-item__cost"><?= format_price($lot['current_price'] ?? 0); ?></span>
                        </div>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span><?= format_price($lot['betting_step'] ?? 0); ?></span>
                        </div>
                    </div>
                    <?php if ($is_authorized_to_place_bid === true): ?>
                    <form class="lot-item__form" method="post" autocomplete="off">
                        <p
                            class="lot-item__form-item form__item <?= isset($errors['cost']) ? 'form__item--invalid' : ''; ?>"
                        >
                            <label for="cost">Ваша ставка</label>
                            <input
                                id="cost"
                                type="text"
                                name="cost"
                                placeholder="<?= format_price($lot['min_bid_price'] ?? 0, ''); ?>"
                                value="<?= htmlspecialchars($form_data['cost'] ?? ''); ?>"
                            >
                            <span class="form__error">
                                <?= format_validation_errors($errors['cost'] ?? []); ?>
                            </span>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                    <?php endif; ?>
                </div>
                <div class="history">
                    <h3>История ставок (<span><?= $bids_total; ?></span>)</h3>
                    <?php if (!empty($bids)): ?>
                    <table class="history__list">
                        <?php foreach ($bids as $bid): ?>
                        <tr class="history__item">
                            <td class="history__name"><?= htmlspecialchars($bid['first_name'] ?? '') ?></td>
                            <td class="history__price"><?= format_price($bid['buy_price'] ?? 0); ?></td>
                            <td class="history__time"><?= to_time_ago_format($bid['created_at'] ?? ''); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>