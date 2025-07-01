<?php

declare(strict_types=1);

/**
 * @var string[] $categories_list Список категорий
 */
?>
<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories_list as $category): ?>
            <li class="nav__item">
                <a href="all-lots.html"><?= $category['name'] ?? ''; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
