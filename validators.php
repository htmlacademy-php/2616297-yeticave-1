<?php

declare(strict_types=1);

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
        return 'Значение не является числом';
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

    return $is_valid ? false : 'Значение обязательно';
}

/**
 * Функция-обёртка, проверяет что данные больше определенного числового значения
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

        return $is_valid ? false : "Значение должно быть больше {$min}";
    };
}