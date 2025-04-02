<?php
session_start();
?>
<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Māju Vieta - Ielogošana</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body id="loginBody">
    <div class="loginKaste">
        <div class="attela">
            <img src="images/login.jpg">
        </div>
        <div class="loginDala">
            <h2>Sveiki, lietotājs!</h2>
            <form action="admin/database/login_funkcija.php" method="post">
                <input type="email" placeholder="E-pasta adrese *" name="epasts" required>
                <div class="paroleKaste">
                    <input type="password" placeholder="Parole *" name="parole" id="parole3" required>
                    <i class="fa-solid fa-eye" id="parslegtParole3"></i>
                </div>
                <div class="pazinojums">
                    <?php
                    if (isset($_SESSION['pazinojumsMV'])) {
                        echo "<p class='login-notif'>" . $_SESSION['pazinojumsMV'] . "</p>";
                        unset($_SESSION['pazinojumsMV']);
                    }
                    ?>
                </div>
                <button type="submit" class="btn" name="ielogoties">Ielogoties</button>
            </form>
            <p>Vēl neesi reģistrēts? <a href="registracija.php">Reģistrējies</a></p>
        </div>
    </div>
</body>

</html>