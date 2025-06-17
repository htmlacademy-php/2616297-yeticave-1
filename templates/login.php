<?php

declare(strict_types=1);

/**
 * @var string[] $categories_list Список категорий
 */
?>
<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php foreach ($categories_list as $category): ?>
                <li class="nav__item">
                    <a href="all-lots.html"><?= $category['name'] ?? ''; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <?php $has_errors = !empty($errors); ?>
    <form
        class="form container <?= $has_errors ? 'form--invalid' : ''; ?>"
        method="post"
        autocomplete="off"
    >
        <h2>Вход</h2>
        <div class="form__item <?= isset($errors['email']) ? 'form__item--invalid' : ''; ?>">
            <label for="email">E-mail <sup>*</sup></label>
            <input
                id="email"
                type="text"
                name="email"
                placeholder="Введите e-mail"
                value="<?= htmlspecialchars($form_data['email'] ?? ''); ?>"
            >
            <span class="form__error">
                <?= format_validation_errors($errors['email'] ?? []); ?>
            </span>
        </div>
        <div
            class="form__item form__item--last <?= isset($errors['password']) ? 'form__item--invalid' : ''; ?>"
        >
            <label for="password">Пароль <sup>*</sup></label>
            <input id="password" type="password" name="password" placeholder="Введите пароль">
            <span class="form__error">
                <?= format_validation_errors($errors['password'] ?? []); ?>
            </span>
        </div>
        <button type="submit" class="button">Войти</button>
    </form>
</main>