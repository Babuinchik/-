<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

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
    <a href="account.php"><img class="profile" src="Img/profile icon.png"></a>
  </div>
  <div class="content">
    <img class="mainimg" src="Img/Main page.png">
    <div class="imgslider">
      <img class="imgmainslider" src="Img/img main slider.png">
      <div class = "content">
        <img class = "mainimg" src = "Img/Main page.png">
        <div class = "imgslider">
            <img class = "imgmainslider"  src = "Img/img main slider.png">
        <div class="items">
            <div class="item active">
                <img src="Img/main slide 2.png">
            </div>
            <div class="item next">
                <img src="Img/main slide1.png">
            </div>
            <div class="item">
                <img src="Img/main slide 2.png">
            </div>
            <div class="item">
                <img src="Img/main slide1.png">
            </div>
            <div class="item prev">
                <img src="Img/main slide 3.png">
            </div>
            <div class="button-container">
                <div class="button"><i class="fas fa-angle-left"><p class = "leftb">←</p></i></div>
                <div class="button"><i class="fas fa-angle-right"></i><p class="rightb">→</p></div>
            </div>
        </div>
        </div>
    </div>
    </div>
  </div>
  <script src="script/my.js"></script>
</body>
<?php include("footer.php")?>

</html>