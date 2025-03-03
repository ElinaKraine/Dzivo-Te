<?php
    session_start();
    
    if (!isset($_SESSION['lietotajaLomaMV']) || ($_SESSION['lietotajaLomaMV'] !== 'Administrators' && $_SESSION['lietotajaLomaMV'] !== 'Moderators')) { 
        header("Location: index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Māju Vieta - Admin daļa</title>
</head>
<body>
    <a class="btn" href="./database/logout.php">Log out</a>
</body>
</html>