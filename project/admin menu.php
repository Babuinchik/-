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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
    rel="stylesheet">
</head>

<body>
  <div class="header">
    <img class="headerimg" src="Img/headerimg.png">
    <a href="main acc.php"><img class="icon" src="Img/icon.jpg"></a>
    <a href="menu acc.php"><button class="dishmenu">Меню</button></a>
    <a href="booking acc.php"><button class="Booking">Бронирование стола</button></a>
    <a href="admin.php"><img class="profile" src="Img/admin icon.png"></a>
  </div>
  <div class="content">
    <img class="mainimg" src="Img/menu.png">
    <div class="imgslider">
      <div class="wall">
        <div class="slider">
          <button class="back">←</button>
          <img class="menuslide1" src="Img/menu slide 1.png">
          <img class="menuslide2" src="Img/menu slide 2.png">
          <img class="menuslide3" src="Img/menu slide 3.png">
          <button class="next">→</button>
          <button class = "edit">Редактировать</button>
        </div>
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