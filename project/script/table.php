<?php

// Функция для получения данных из базы данных SQLite
function get_data_from_db($db_path, $table_name) {
    try {
        $db = new SQLite3($db_path);

        // Получаем имена столбцов таблицы
        $query = "PRAGMA table_info(" . $table_name . ")";
        $result = $db->query($query);
        $columns = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $columns[] = $row['name'];
        }

        // Извлекаем все данные из таблицы
        $query = "SELECT * FROM " . $table_name;
        $result = $db->query($query);

        $data = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;  // Каждый элемент - ассоциативный массив (словарь)
        }

        $db->close();
        return $data;

    } catch (Exception $e) {
        echo "Ошибка при извлечении данных из базы данных: " . $e->getMessage() . "<br>";
        return [];
    }
}

// Путь к базе данных и имя таблицы
$db_path = 'mydatabase.db'; // Замените на путь к вашей базе данных SQLite
$table_name = 'booking table'; // Замените на имя вашей таблицы

// Получаем данные
$data = get_data_from_db($db_path, $table_name);

// Функция для отображения данных в HTML-таблице
function display_data_in_table($data, $columns) {
    if (!empty($data)) {
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        foreach ($columns as $column) {
            echo '<th>' . htmlspecialchars($column) . '</th>';
        }
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($data as $row) {
            echo '<tr>';
            foreach ($columns as $column) {
                echo '<td>' . htmlspecialchars($row[$column]) . '</td>'; // Преобразуем спецсимволы для безопасности
            }
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>Нет данных для отображения.</p>';
    }
}

// Отображаем данные в таблице
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Table</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>Data from Database</h1>

<?php
    if ($data) {
        $columns = array_keys($data[0]); // Получаем имена столбцов из первого элемента
        display_data_in_table($data, $columns);
    } else {
        echo "<p>Нет данных для отображения.</p>";
    }
?>

</body>
</html>

