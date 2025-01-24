<?php
    $page = "saglabati";
    require "assets/header.php";
    
    if (!isset($_SESSION['lietotajaLomaMV'])) {
        header("Location: index.php");
        exit();
    }
?>


<?php
    require "assets/footer.php";
?>