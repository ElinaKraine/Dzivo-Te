<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Māju Vieta</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <header>
        <a href="./" class="logo"><img src="images/logo.png"> Māju vieta</a>
        <nav class="navbar">
            <a href="./" class="<?php echo ($page == 'sakums' ? 'current' : '') ?>">Sākumlapa</a>
            <a href="parmums.php" class="<?php echo ($page == 'parmums' ? 'current' : '') ?>">Par mums</a>
            <a href="majas.php" class="<?php echo ($page == 'majas' ? 'current' : '') ?>">Mājās</a>
            <a href="dzivokli.php" class="<?php echo ($page == 'dzivokli' ? 'current' : '') ?>">Dzīvokļi</a>
        </nav>
        <a class="btn">Ielogoties</a>
    </header>