<?php

declare(strict_types=1);

/**
 * @var string $categories_header HTML-представление категорий в шапке
 */
?>
<main>
    <?= $categories_header; ?>
    <section class="rates container">
        <h2>Мои ставки</h2>
        <?php if (!empty($bids)): ?>
        <table class="rates__list">
            <?php
            foreach ($bids as $bid):
                $time_ago = to_time_ago_format($bid['last_buy_time']);
                $end_date = get_dt_range($bid['end_date']);
                $time_left = implode(':', $end_date);
                $user_won = $bid['is_winner'] !== null;
                $is_finished = $end_date['hours'] === 0 && $end_date['minutes'] === 0 && $end_date['seconds'] === 0;
                $is_less_than_hour_left = $end_date['hours'] === 0;

                $bid_modifier = match (true) {
                    $user_won => 'rates__item--win',
                    $is_finished => 'rates__item--end',
                    default => '',
                };

                $timer_content = match (true) {
                    $user_won => 'Ставка выиграла',
                    $is_finished => 'Торги окончены',
                    default => $time_left,
                };

                $timer_modifier = match (true) {
                    $user_won => 'timer--win',
                    $is_finished => 'timer--end',
                    $is_less_than_hour_left => 'timer--finishing',
                    default => '',
                };
            ?>
            <tr class="rates__item <?= $bid_modifier; ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img
                            src="<?= $bid['img_url'] ?? ''; ?>"
                            width="54"
                            height="40"
                            alt="<?= htmlspecialchars($bid['name'] ?? ''); ?>"
                        >
                    </div>
                    <h3 class="rates__title">
                        <a href="/lot.php?id=<?= $bid['id'] ?? 0; ?>">
                            <?= htmlspecialchars($bid['name'] ?? ''); ?>
                        </a>
                    </h3>
                </td>
                <td class="rates__category">
                    <?= $bid['category_name'] ?? ''; ?>
                </td>
                <td class="rates__timer">
                    <div class="timer <?= $timer_modifier; ?>">
                        <?= $timer_content; ?>
                    </div>
                </td>
                <td class="rates__price">
                    <?= format_price($bid['current_price'] ?? ''); ?>
                </td>
                <td class="rates__time">
                    <?= $time_ago; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </section>
</main>
