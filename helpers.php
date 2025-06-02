<?php

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date): bool
{
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            $type = match (true) {
                is_int($value) => 'i',
                is_string($value) => 's',
                is_double($value) => 'd'
            };

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form(int $number, string $one, string $two, string $many): string
{
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = [])
{
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Форматирует цену добавляя разделители для тысяч и символ рубля
 *
 * @param int $price Не отформатированная цена
 * @return string Отформатированная цена с символом рубля
 */
function format_price(int $price): string
{
    $formatted_price = number_format($price, 0, '', ' ');
    return "$formatted_price ₽";
}

/**
 * Возвращает количество часов и минут с текущего времени до переданной даты
 * в формате ассоциативного массива. В случае если дата конца временного промежутка
 * уже прошла, возвращает массив нулевых значений
 *
 * @param string $string_date Дата конца временного промежутка в формате строки
 * @return array<string, int> Ассоциативный массив, количество часов и минут временного интервала
 */
function get_dt_range(string $string_date): array
{
    $current_time = date_create('now');
    $end_date = date_create($string_date);

    if ($end_date <= $current_time) {
        return [
            'hours' => 0,
            'minutes' => 0,
        ];
    }

    $range = date_diff($current_time, $end_date);

    $total_hours = ($range->d * 24) + $range->h;
    $total_minutes = $range->i;

    return [
        'hours' => $total_hours,
        'minutes' => $total_minutes,
    ];
}

/**
 * Функция форматирования массива временного интервала в строку
 *
 * @param array<string, int> $dt_range Ассоциативный массив, количество часов и минут временного интервала
 * @return string Строка в формате ЧЧ:ММ
 */
function format_dt_range(array $dt_range): string
{
    $formatted_hours = sprintf(
        "%02d",
        $dt_range['hours'] ?? 0,
    );
    $formatted_minutes = sprintf(
        "%02d",
        $dt_range['minutes'] ?? 0,
    );

    return "$formatted_hours:$formatted_minutes";
}

/**
 * Завершает работу программы с сообщением об ошибке
 *
 * @param string $message Сообщение об ошибке
 * @param int $code Код ошибки сервера
 * @return void
 */
function exit_with_message(string $message, int $code = 500): void
{
    http_response_code($code);
    die($message);
}

/**
 * Выполняет SQL запрос
 *
 * @param mysqli $conn Ресурс подключения в БД
 * @param string $sql Текст подготовленного запроса
 * @param array $data Выходные переменные для привязки к запросу
 * @return array Данные в формате ассоциативного массива, заканчивает выполнение PHP-сценария в случае ошибки
 */
function execute_query(mysqli $conn, string $sql, array $data = []): array
{
    $stmt = db_get_prepare_stmt(
        $conn,
        $sql,
        $data,
    );

    $stmt_result = $stmt->execute();

    if ($stmt_result === false) {
        exit_with_message('Ошибка в обработке запроса. Пожалуйста, попробуйте позже.');
    }

    return $stmt->get_result()
        ->fetch_all(MYSQLI_ASSOC);
}