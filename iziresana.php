<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["apstiprinat"])) {
    require "admin/database/con_db.php";

    $lietotaja_id = $_SESSION['lietotajaIdDt'];
    $ip_adrese = $_SERVER['REMOTE_ADDR'];

    $id_majuvieta_iret = intval($_POST['id_majuvieta_iret']);
    $registresanasDatums = $_POST['registresanasDatums'];
    $izrakstisanasDatums = $_POST['izrakstisanasDatums'];
    $cena = $_POST['cena'];

    $parbaudijums = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_iziresana WHERE (id_lietotajs = ? AND id_majuvieta_iret = ? AND registresanas_datums = ? AND izrakstisanas_datums = ?) OR (id_majuvieta_iret = ? AND registresanas_datums = ? AND izrakstisanas_datums = ?)");
    $parbaudijums->bind_param("iississ", $lietotaja_id, $id_majuvieta_iret, $registresanasDatums, $izrakstisanasDatums, $id_majuvieta_iret, $registresanasDatums, $izrakstisanasDatums);
    $parbaudijums->execute();
    $parbaudijums->bind_result($count);
    $parbaudijums->fetch();
    $parbaudijums->close();

    if ($count > 0) {
        $_SESSION['pazinojumsMV'] = "Kļūda, veidojot nomas ierakstu!";
    } else {
        $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_iziresana (id_lietotajs, id_majuvieta_iret, registresanas_datums, izrakstisanas_datums, cena, ip_adrese) VALUES (?, ?, ?, ?, ?, ?)");
        $vaicajums->bind_param("iissds", $lietotaja_id, $id_majuvieta_iret, $registresanasDatums, $izrakstisanasDatums, $cena, $ip_adrese);
        if ($vaicajums->execute()) {
            $_SESSION['pazinojumsMV'] = "Izīrēšanas veiksmīgi izveidota!";
        } else {
            $_SESSION['pazinojumsMV'] = "Kļūda sistemā!";
        }
        $vaicajums->close();
        $savienojums->close();
    }
}

header("Location: https://kristovskis.lv/3pt2/kraine/Dzivo-Te/majas.php");
