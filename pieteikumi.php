<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nosutit"])) {
    require "admin/database/con_db.php";

    $epasts = $_SESSION['lietotajsMV'];
    $ip_adrese = $_SERVER['REMOTE_ADDR'];

    if (!empty($epasts)) {
        $id_majuvieta_pirkt = intval($_POST['id_majuvieta_pirkt']);

        $parbaudijums = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_pieteikumi WHERE epasts = ? AND id_majuvieta_pirkt = ?");
        $parbaudijums->bind_param("si", $epasts, $id_majuvieta_pirkt);
        $parbaudijums->execute();
        $parbaudijums->bind_result($count);
        $parbaudijums->fetch();
        $parbaudijums->close();

        if ($count > 0) {
            $_SESSION['pazinojumsMV'] = "Jūs jau esat nosūtījis pieteikumu par šo māju!";
        } else {
            $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_pieteikumi (epasts, id_majuvieta_pirkt, statuss, ip_adrese) VALUES (?, ?, default, ?)");
            $vaicajums->bind_param("sis", $epasts, $id_majuvieta_pirkt, $ip_adrese);
            if ($vaicajums->execute()) {
                $_SESSION['pazinojumsMV'] = "Pieteikums veiksmīgi nosutīts!";
            } else {
                $_SESSION['pazinojumsMV'] = "Kļūda sistemā!";
            }
            $vaicajums->close();
            $savienojums->close();
        }
    } else {
        $_SESSION['pazinojumsMV'] = "Kļūda sistemā!";
    }
}

header("Location: https://kristovskis.lv/3pt2/kraine/Dzivo-Te/maja.php?id=" . $id_majuvieta_pirkt);
