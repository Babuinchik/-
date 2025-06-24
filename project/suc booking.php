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
<?php include("header.php")?>
  <div class="content">
    <img class="mainimg" src="Img/booking.png">
    <div class="imgslider">
      <img class="" src="">
      <div class="wallbooking">
        <div class="">
          <p class="Bookingtext">Вы забронировали стол</p>
          <br>
          <p class="sucbookingtext">Дата:</p>
          <br>
          <p class="sucbookingtext">На кого забронирован:</p>
          <a href="index.php"><button class="bookingbutton">вернуться на главную</button></a>
        </div>
      </div>
    </div>
  </div>
</body>
<?php include("footer.php")?>

</html>