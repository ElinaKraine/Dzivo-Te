<?php
require 'con_db.php';

if (isset($_POST['registracija'])) {
    session_start();

    $epasts = htmlspecialchars($_POST["epastaAdrese"]);
    $vaicajums = "SELECT epasts FROM majuvieta_lietotaji WHERE epasts = '$epasts'";
    $rezultatsEpasts = mysqli_query($savienojums, $vaicajums);

    $vards = htmlspecialchars($_POST["vards"]);
    $uzvards = mysqli_real_escape_string($savienojums, $_POST["uzvards"]);
    $vaicajums = "SELECT vards, uzvards FROM majuvieta_lietotaji WHERE vards = '$vards' AND uzvards = '$uzvards'";
    $rezultatsVardsUzvards = mysqli_query($savienojums, $vaicajums);

    $talrunis = htmlspecialchars($_POST["talrunis"]);
    $vaicajums = "SELECT talrunis FROM majuvieta_lietotaji WHERE talrunis = '$talrunis'";
    $rezultatsTalrunis = mysqli_query($savienojums, $vaicajums);

    $parole1 = mysqli_real_escape_string($savienojums, $_POST["paroleR"]);
    $parole2 = mysqli_real_escape_string($savienojums, $_POST["paroleAtkartoti"]);
    $password_pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/";

    if (!empty($epasts) && !empty($vards) && !empty($uzvards) && !empty($talrunis) && !empty($parole1) && !empty($parole2)) {
        if (mysqli_num_rows($rezultatsEpasts) > 0) {
            $_SESSION['pazinojumsMV'] = "Šis e-pasts jau eksistē!";
            header("location: ../../registracija.php");
        } else if (mysqli_num_rows($rezultatsVardsUzvards) > 0) {
            $_SESSION['pazinojumsMV'] = "Lietotājs ar šo vārdu un uzvārdu jau eksistē!";
            header("location: ../../registracija.php");
        } else if (mysqli_num_rows($rezultatsTalrunis) > 0) {
            $_SESSION['pazinojumsMV'] = "Šis tālrunis jau eksistē!";
            header("location: ../../registracija.php");
        } else if ($parole1 != $parole2) {
            $_SESSION['pazinojumsMV'] = "Paroli nav vienādi!";
            header("location: ../../registracija.php");
        } else if (!preg_match($password_pattern, $parole1)) {
            $_SESSION['pazinojumsMV'] = "Parole jābūt vismaz 8 rakstzīmēm, ar vismaz vienu mazo burtu, vienu lielo burtu un skaitli!";
            header("location: ../../registracija.php");
        } else {
            $sifrets_parole = password_hash($parole1, PASSWORD_DEFAULT);

            $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_lietotaji(vards, uzvards, epasts, talrunis, parole) VALUES (?, ?, ?, ?, ?)");
            $vaicajums->bind_param("sssss", $vards, $uzvards, $epasts, $talrunis, $sifrets_parole);
            if ($vaicajums->execute()) {
                $_SESSION['pazinojumsMVL'] = "Reģistrācija veiksmīgi pabeigta!";
            } else {
                $_SESSION['pazinojumsMVL'] = "Kļūda sistemā!";
            }
            $vaicajums->close();
            $savienojums->close();

            header("location: ../../registracija.php");
        }
    } else {
        $_SESSION['pazinojumsMV'] = "Jāaizpilda visi obligātie lauki! *";
        header("location: ../../registracija.php");
    }
}
