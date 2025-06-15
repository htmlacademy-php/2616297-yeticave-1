<?php

declare(strict_types=1);

/**
 * @var string[] $categories_list Список категорий
 * @var array<string, string[]> $errors Ассоциативный массив ошибок валидации
 * @var array<string, mixed> $form_data Массив заполненных данных формы
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
        <h2>Регистрация нового аккаунта</h2>
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
        <div class="form__item <?= isset($errors['password']) ? 'form__item--invalid' : ''; ?>">
            <label for="password">Пароль <sup>*</sup></label>
            <input
                id="password"
                type="password"
                name="password"
                placeholder="Введите пароль"
                value="<?= htmlspecialchars($form_data['password'] ?? ''); ?>"
            >
            <span class="form__error">
                <?= format_validation_errors($errors['password'] ?? []); ?>
            </span>
        </div>
        <div class="form__item <?= isset($errors['name']) ? 'form__item--invalid' : ''; ?>">
            <label for="name">Имя <sup>*</sup></label>
            <input
                id="name"
                type="text"
                name="name"
                placeholder="Введите имя"
                value="<?= htmlspecialchars($form_data['name'] ?? ''); ?>"
            >
            <span class="form__error">
                <?= format_validation_errors($errors['name'] ?? []); ?>
            </span>
        </div>
        <div class="form__item <?= isset($errors['message']) ? 'form__item--invalid' : ''; ?>">
            <label for="message">Контактные данные <sup>*</sup></label>
            <textarea
                id="message"
                name="message"
                placeholder="Напишите как с вами связаться"
            ><?= htmlspecialchars($form_data['message'] ?? ''); ?></textarea>
            <span class="form__error">
                <?= format_validation_errors($errors['message'] ?? []); ?>
            </span>
        </div>
        <span class="form__error form__error--bottom">
            <?= !empty($errors) ? 'Пожалуйста, исправьте ошибки в форме.' : ''; ?>
        </span>
        <button type="submit" class="button">Зарегистрироваться</button>
        <a class="text-link" href="#">Уже есть аккаунт</a>
    </form>
</main>