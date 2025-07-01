<?php

declare(strict_types=1);

/**
 * @var string $categories_header HTML-представление категорий в шапке
 * @var string[] $categories_list Список категорий
 * @var array<string, string[]> $errors Ассоциативный массив ошибок валидации
 * @var array<string, mixed> $form_data Массив заполненных данных формы
 */
?>
<main>
    <?= $categories_header; ?>
    <?php $has_errors = !empty($errors); ?>
    <form
        class="form form--add-lot container <?= $has_errors ? 'form--invalid' : ''; ?>"
        method="post"
        enctype="multipart/form-data"
    >
        <h2>Добавление лота</h2>
        <div class="form__container-two">
            <div class="form__item <?= isset($errors['lot-name']) ? 'form__item--invalid' : ''; ?>">
                <label for="lot-name">Наименование <sup>*</sup></label>
                <input
                    id="lot-name"
                    type="text"
                    name="lot-name"
                    placeholder="Введите наименование лота"
                    value="<?= htmlspecialchars($form_data['lot-name'] ?? ''); ?>"
                >
                <span class="form__error"><?= format_validation_errors($errors['lot-name'] ?? []); ?></span>
            </div>
            <div class="form__item <?= isset($errors['category']) ? 'form__item--invalid' : ''; ?>">
                <label for="category">Категория <sup>*</sup></label>
                <select id="category" name="category">
                    <option
                        value=""
                        <?= !isset($form_data['category']) ? 'selected' : ''; ?>
                    >
                        Выберите категорию
                    </option>
                    <?php

                    foreach($categories_list as $category): ?>
                    <option
                        value="<?= htmlspecialchars($category['slug'] ?? ''); ?>"
                        <?= ($form_data['category'] ?? '') === ($category['slug'] ?? '') ? 'selected' : ''; ?>
                    >
                        <?= htmlspecialchars($category['name'] ?? ''); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <span class="form__error"><?= format_validation_errors($errors['category'] ?? []); ?></span>
            </div>
        </div>
        <div class="form__item form__item--wide <?= isset($errors['message']) ? 'form__item--invalid' : ''; ?>">
            <label for="message">Описание <sup>*</sup></label>
            <textarea
                id="message"
                name="message"
                placeholder="Напишите описание лота"
            ><?= htmlspecialchars($form_data['message'] ?? ''); ?></textarea>
            <span class="form__error"><?= format_validation_errors($errors['message'] ?? []); ?></span>
        </div>
        <div class="form__item form__item--file <?= isset($errors['lot-img']) ? 'form__item--invalid' : ''; ?>">
            <label>Изображение <sup>*</sup></label>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" id="lot-img" name="lot-img" value="">
                <label for="lot-img">
                    Добавить
                </label>
            </div>
            <span class="form__error"><?= format_validation_errors($errors['lot-img'] ?? []); ?></span>
        </div>
        <div class="form__container-three">
            <div class="form__item form__item--small <?= isset($errors['lot-rate']) ? 'form__item--invalid' : ''; ?>">
                <label for="lot-rate">Начальная цена <sup>*</sup></label>
                <input
                    id="lot-rate"
                    type="text"
                    name="lot-rate"
                    value="<?= htmlspecialchars($form_data['lot-rate'] ?? ''); ?>"
                    placeholder="0"
                >
                <span class="form__error"><?= format_validation_errors($errors['lot-rate'] ?? []); ?></span>
            </div>
            <div class="form__item form__item--small <?= isset($errors['lot-step']) ? 'form__item--invalid' : ''; ?>">
                <label for="lot-step">Шаг ставки <sup>*</sup></label>
                <input
                    id="lot-step"
                    type="text"
                    name="lot-step"
                    placeholder="0"
                    value="<?= htmlspecialchars($form_data['lot-step'] ?? ''); ?>"
                >
                <span class="form__error"><?= format_validation_errors($errors['lot-step'] ?? []); ?></span>
            </div>
            <div class="form__item <?= isset($errors['lot-date']) ? 'form__item--invalid' : ''; ?>">
                <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
                <input
                    class="form__input-date"
                    id="lot-date"
                    type="text"
                    name="lot-date"
                    placeholder="Введите дату в формате ГГГГ-ММ-ДД"
                    value="<?= htmlspecialchars($form_data['lot-date'] ?? ''); ?>"
                >
                <span class="form__error"><?= format_validation_errors($errors['lot-date'] ?? []); ?></span>
            </div>
        </div>
        <span class="form__error form__error--bottom">
            <?= !empty($errors) ? 'Пожалуйста, исправьте ошибки в форме.' : ''; ?>
        </span>
        <button type="submit" class="button">Добавить лот</button>
    </form>
</main>