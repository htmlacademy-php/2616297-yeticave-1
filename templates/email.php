<?php

declare(strict_types=1);

?>
<h1>Поздравляем с победой</h1>
<p>Здравствуйте, <?= htmlspecialchars($user_name ?? ''); ?></p>
<p>
    Ваша ставка для лота <a href="<?= $project_url; ?>/lot.php?id=<?= $lot_id ?? 0; ?>">
        <?= htmlspecialchars($lot_name ?? ''); ?></a> победила.
</p>
<p>Перейдите по ссылке <a href="<?= $project_url; ?>/my-bets.php">мои ставки</a>,
    чтобы связаться с автором объявления</p>
<small>Интернет-Аукцион "YetiCave"</small>