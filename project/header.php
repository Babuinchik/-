<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Кочевник</title>
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
  <div class="header">
    <img class="headerimg" src="img/headerimg.png">
    <a href="index.php"><img class="icon" src="img/icon.jpg"></a>
    <a href="menu.php"><button class="dishmenu">Меню</button></a>
    <a href="booking.php"><button class="Booking">Бронирование стола</button></a>
    <?php if(isset($_SESSION['user'])): ?>
      <a href="account.php"><img class="profile" src="img/profile icon.png"></a>
    <?php else: ?>
      <a href="enter.php"><button class="enter">Вход</button></a>
    <?php endif; ?>
  </div>