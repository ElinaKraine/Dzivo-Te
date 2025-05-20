<?php
session_start();
?>
<!DOCTYPE html>
<html lang="lv">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Māju Vieta - Reģistrēšana</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body id="registracijaBody">
    <div class="registracijasKaste">
        <div class="majuVieta">
            <img src="images/logo.png">
            <p>Dzīvo Te</p>
        </div>
        <h2>Reģistrēšana</h2>
        <form action="admin/database/registracija_funkcija.php" method="post">
            <div id="vardsUzvards">
                <input type="text" name="vards" placeholder="Vārds *">
                <input type="text" name="uzvards" placeholder="Uzvārds *">
            </div>
            <input type="email" name="epastaAdrese" placeholder="E-pasta adrese *" required>
            <input type="text" name="talrunis" placeholder="Tālrunis *" required>
            <div class="paroleKaste">
                <input type="password" name="paroleR" id="parole1" placeholder="Parole *" required>
                <i class="fa-solid fa-eye" id="parslegtParole1"></i>
            </div>
            <div class="paroleKaste">
                <input type="password" name="paroleAtkartoti" id="parole2" placeholder="Parole (atkārtoti) *" required>
                <i class="fa-solid fa-eye" id="parslegtParole2"></i>
            </div>
            <div class="pazinojums">
                <?php
                if (isset($_SESSION['pazinojumsMV'])) {
                    echo "<p class='registr-notif'>" . $_SESSION['pazinojumsMV'] . "</p>";
                    unset($_SESSION['pazinojumsMV']);
                }
                ?>
            </div>
            <button type="submit" class="btn" name="registracija">Reģistrēties</button>
        </form>
        <a href="login.php"><i class="fa-solid fa-arrow-left"></i> Atpakaļ uz ielogošanu</a>
    </div>

    <?php if (isset($_SESSION['pazinojumsMVL'])): ?>
        <div class="modal modal-active" id="modal-message">
            <div class="modal-box">
                <div class="close-modal" data-target="#modal-message"><i class="fas fa-times"></i></div>
                <h2>
                    <?php
                    echo $_SESSION['pazinojumsMVL'];
                    unset($_SESSION['pazinojumsMVL']);
                    ?>
                </h2>
            </div>
        </div>
    <?php endif; ?>
</body>

</html>