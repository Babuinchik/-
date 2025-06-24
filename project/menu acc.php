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
    <div class = "content">
        <img class = "mainimg" src = "Img/menu.png">
        <div class = "imgslider">
            <div class = "wall">
                <div class="items">
                    <div class="item active">
                        <img src="Img/menu slide 1.png">
                    </div>
                    <div class=" item next">
                        <img src="Img/menu slide 2.png">
                    </div>
                    <div class="item">
                        <img src="Img/menu slide 3.png">
                    </div>
                    <div class="item">
                        <img src="Img/menu slide 4.png">
                    </div>
                    <div class="item prev">
                        <img src="Img/menu slide 5.png">
                    </div>
                    <div class="item active">
                        <img src="Img/menu slide 6.png">
                    </div>
                    <div class=" item next">
                        <img src="Img/menu slide 7.png">
                    </div>
                    <div class="item">
                        <img src="Img/menu slide 8.png">
                    </div>
                    <div class="item">
                        <img src="Img/menu slide 9.png">
                    </div>
                    <div class="item prev">
                        <img src="Img/menu slide 10.png">
                    </div>
                    <div class="button-container">
                        <div class="button"><i class="fas fa-angle-left"><p class = "leftb">←</p></i></div>
                        <div class="button"><i class="fas fa-angle-right"></i><p class="rightb">→</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="script/my2.js"></script>
</body>
<?php include("footer.php")?>

</html>