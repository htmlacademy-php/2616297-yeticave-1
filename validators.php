<?php

declare(strict_types=1);

require_once 'helpers.php';
require_once 'models/user.php';

/**
 * Функция-валидатор, проверяет что данные являются целым числом
 *
 * @param mixed $value Данные для валидации
 * @return string|bool Сообщение ошибки, либо false, в случае когда данные валидны
 */
function valid_integer(mixed $value): string|bool
{
    if ($value === null) {
        return false;
    }

    $is_valid = filter_var($value, FILTER_VALIDATE_INT);

    if ($is_valid === false) {
        return 'Значение поля не является числом';
    }

    return false;
}

/**
 * Функция-валидатор, проверяет что данные заполнены
 *
 * @param mixed $value Данные для валидации
 * @return string|bool Сообщение ошибки, либо false, в случае когда данные валидны
 */
function required(mixed $value): string|bool
{
    $is_valid = match (true) {
        is_array($value) => !empty($value),
        is_string($value) => trim($value) !== '',
        is_int($value), is_float($value) => true,
        default => false,
    };

    if (
        is_array($value)
        && isset($value['error'])
        && $value['error'] === UPLOAD_ERR_NO_FILE
    ) {
        $is_valid = false;
    }

    return $is_valid ? false : 'Поле обязательно к заполнению';
}

/**
 * Функция-валидатор, проверяет что данные больше определенного числового значения
 *
 * @param int $min Минимальное числовое значение валидных данных
 * @return callable Функция-валидатор
 */
function greater_than(int $min): callable
{
    return function (mixed $value) use ($min): string|bool {
        if ($value === null) {
            return false;
        }

        if (
            is_string($value)
            && is_numeric($value)
        ) {
            $value = str_contains($value, '.') ? (float)$value : (int)$value;
        }

        $is_valid = match (true) {
            is_array($value) => count($value) > $min,
            is_int($value), is_double($value), is_float($value) => $value > $min,
            is_string($value) => mb_strlen($value) > $min,
            default => false,
        };

        return $is_valid ? false : "Значение поля должно быть больше {$min}";
    };
}

/**
 * Возвращает валидатор, который проверяет что строка не превышает заданные лимит количества символов
 *
 * @param int $max Лимит количества символов
 * @return callable Функция-валидатор
 */
function character_limit(int $max): callable
{
    return function (mixed $value) use ($max): string|bool {
        if ($value === null) {
            return false;
        }

        $value = (string)$value;

        if (mb_strlen($value) > $max) {
            return "Поле не должно быть больше $max символов";
        }

        return false;
    };
}

/**
 * Проверяет что значение имеет корректный формат даты
 *
 * @param mixed $value Данные для валидации
 * @return string|bool Сообщение об ошибке, либо false если значение проходит валидацию
 */
function date_convertable(mixed $value): string|bool
{
    if ($value === null) {
        return false;
    }

    $is_convertable = is_date_valid((string)$value);

    if ($is_convertable === false) {
        return 'Необходимый формат даты - ГГГГ-ММ-ДД';
    }

    return false;
}

/**
 * Проверяет что файл имеет подходящий mime тип
 *
 * @param array $mime_types Массив с разрешёнными mime типами
 * @return callable Функция-валидатор
 */
function mime_type_in(array $mime_types): callable
{
    return function (mixed $value) use ($mime_types): string|bool {
        if ($value === null) {
            return false;
        }

        $file_name = $value['tmp_name'] ?? '';

        if (!is_uploaded_file($file_name)) {
            return false;
        }

        $mime_type = mime_content_type($file_name);

        if ($mime_type === false) {
            return false;
        }

        $allowed_extensions = mime_to_ext($mime_types);

        if (!in_array($mime_type, $mime_types)) {
            return 'Допустимые форматы файла: ' . implode(', ', $allowed_extensions);
        }

        return false;
    };
}

/**
 * Проверят что дата больше текущей даты на заданное количество часов
 *
 * @param int $hours Количество часов, через которые должна быть дата
 * @return callable Функция-валидатор
 */
function hours_after_now(int $hours): callable
{
    return function (mixed $value) use ($hours): string|bool {
        if ($value === null) {
            return false;
        }

        $dt_range = get_dt_range($value);
        $hours_after_now = (int)($dt_range['hours'] ?? 0);

        if ($hours_after_now < $hours) {
            $required_time = (new DateTime())
                ->add(new DateInterval("PT{$hours}H"))
                ->format('d.m.Y H:i');

            return "Дата должна быть не раньше {$required_time} (на {$hours} "
                   . get_noun_plural_form($hours, 'час', 'часа', 'часов') . " позже текущего времени)";
        }

        return false;
    };
}

function valid_email(mixed $value): string|bool
{
    if ($value === null) {
        return false;
    }

    if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
        return 'Введите корректный e-mail';
    }

    return false;
}

function unique_email(mysqli $conn): callable
{
    return function (mixed $value) use ($conn): string|bool {
        if ($value === null) {
            return false;
        }

        if (is_email_exists($conn, $value) === true) {
            return 'E-mail занят';
        }

        return false;
    };
}