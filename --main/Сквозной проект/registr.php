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
  <a href="index.html"><img class="icon" src="Img/icon.jpg"></a>
  <div class="enterback">
    <div class="wallbooking">
      <div class="">
        <p class="Bookingtextreg">Регистрация</p>
        <br>
        <?php 
        include ("registr.php")
        ?>
      <input type="email" placeholder="E-mail">
      <br>
      <input type="number" placeholder="Номер телефона">
      <br>
      <input type="text" placeholder="Имя">
      <br>
      <input type="text" placeholder="Фамилия">
      <br>
      <input type="password" placeholder="Пароль">
      <br>
      <input type="password" placeholder="Подтверждение пароля">
        <a href = "Enter.html"><button class="bookingbutton">Регистрация</button></a>
        <br>
        <p class="anoreg">Eсть аккаунта? <a href="Enter.html">Войдите!</a></p>
      </div>
    </div>
  </div>


</body>

</html>