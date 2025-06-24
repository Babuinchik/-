<?php
include("config.php");
include("function.php");
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
    <style>
          html {
      background-image: url(Img/Booking.png);
      background-size: cover;
      background-repeat: no-repeat;
      height: 100%;
    }
    </style>
</head>

<body>
  <div class="header">
    <img class="headerimg" src="Img/headerimg.png">
    <a href="index.php"><img class="icon" src="Img/icon.jpg"></a>
    <a href="Menu.php"><button class="dishmenu">Меню</button></a>
    <a href="Booking.php"><button class="Booking">Бронирование стола</button></a>
    <a href="admin.php"><img class="profile" src="Img/admin icon.png"></a>

  </div>
<div class="tables">
  <table>
    <?php
    include("table.php")
    ?>
    <tr>
      <td>№ Стола</td>
      <td>Статус</td>
      <td>Кем забронирован стол</td>
      <td>Время</td>
      <td>Дата</td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
  </table>
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