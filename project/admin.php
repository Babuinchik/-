<?php
include("config.php");
include("function.php");

// Проверка авторизации, если нужно
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: enter.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кочевник</title>
    <link rel="stylesheet" type="text/css" href="Css/main.css">
    <style>
        
        html{
            background-image: url(img/admin\ menu.png);
            background-size: cover;
            background-repeat: no-repeat;
            height: 100%;
        }

        .backwall {
            display: flex;
            background-color: rgb(86, 109, 42);
            flex-direction: column;
            border-radius: 87px;
            height: 300px;
            width: 800px;
        }
        .main {
            display: flex;
            justify-content: center;
            height: 1100px;
            align-items: center;
        }
        .admintext {
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }

        .adminbutton {
            display: flex;
            flex-direction: column;
            
        }

    </style>
</head>
<body>

<img class="headerimg" src="Img/headerimg.png">
<a href="main acc.php"><img class="icon" src="Img/icon.jpg"></a>
<a href="menu acc.php"><button class="dishmenu">Меню</button></a>
<a href="booking acc.php"><button class="Booking">Бронирование стола</button></a>
<a href="admin.php"><img class="profile" src="Img/admin icon.png"></a>
    <div class = "main">

        <div class = "backwall">
            <div class="admintext">
                <p>Админ панель</p>
                <p>Добро пожаловать в админ панель ресторана “Кочевник”!</p>
            </div> 
        <div class="adminbutton">
                <a href = "customers base.php"><button>Клиентская база</button></a>
                <a href = "admin menu.php"><button>Редактировать меню</button></a>
                <a href = "booking table.php"><button>Посмотреть столы для бронирования</button></a>
        </div>
        </div>
    </div>
</body>
<footer>
    <div class="footer">
        <a class="tel">+7 (911) 654-04-00</a>
        <a class="adr">ул. имени В.И. Ленина, 33Б, Нарьян-Мар</a>
        <a class="work">круглосуточная работа с 12:00 - до 23:00</a>
    </div>
</footer>
</html>