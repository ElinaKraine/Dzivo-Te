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
    <link rel="shortcut icon" href="../images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/style-admin.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <header class="header-admin">
        <a href="./" class="logo"><img src="../images/logo.png"> Dzīvo Te</a>
        <a class="btn" href="./database/logout.php">Izlogoties</a>
    </header>
    <div class="sidebar">
        <a href="./" class="<?php echo ($page == 'sakums' ? 'current' : '') ?>">Sākumlapa</a>
        <a href="./lietotaji.php" class="<?php echo ($page == 'lietotaji' ? 'current' : '') ?>">Lietotāji</a>
        <a href="./sludinajumi_pirkt.php" class="<?php echo ($page == 'pardosanas' ? 'current' : '') ?>">Pārdošanas sludinājumi</a>
        <a href="./sludinajumi_iret.php" class="<?php echo ($page == 'iziresanas' ? 'current' : '') ?>">Izīrēšanas sludinājumi</a>
        <a href="./pieteikumi.php" class="<?php echo ($page == 'pieteikumi' ? 'current' : '') ?>">Pieteikumi iegadei</a>
        <a href="./iziresanas.php" class="<?php echo ($page == 'ires' ? 'current' : '') ?>">Īres ieraksti</a>
    </div>
    <div class="blakusSidebar">