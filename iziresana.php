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

    $parbaude = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_iziresana WHERE (id_lietotajs = ? AND id_majuvieta_iret = ? AND registresanas_datums = ? AND izrakstisanas_datums = ?) OR (id_majuvieta_iret = ? AND registresanas_datums = ? AND izrakstisanas_datums = ?)");
    $parbaude->bind_param("iississ", $lietotaja_id, $id_majuvieta_iret, $registresanasDatums, $izrakstisanasDatums, $id_majuvieta_iret, $registresanasDatums, $izrakstisanasDatums);
    $parbaude->execute();
    $parbaude->bind_result($count);
    $parbaude->fetch();
    $parbaude->close();

    $parbaude = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_iziresana WHERE id_lietotajs = ? 
        AND ((registresanas_datums <= ? AND izrakstisanas_datums >= ?) OR
            (registresanas_datums <= ? AND izrakstisanas_datums >= ?) OR
            (registresanas_datums >= ? AND izrakstisanas_datums <= ?))");
    $parbaude->bind_param(
        "issssss",
        $lietotaja_id,
        $registresanasDatums,
        $registresanasDatums,
        $izrakstisanasDatums,
        $izrakstisanasDatums,
        $registresanasDatums,
        $izrakstisanasDatums
    );
    $parbaude->execute();
    $parbaude->bind_result($parklasanasSkaits);
    $parbaude->fetch();
    $parbaude->close();

    $vaicajums = $savienojums->prepare("SELECT id_ipasnieks FROM majuvieta_iret WHERE iret_id = ?");
    $vaicajums->bind_param("i", $id_majuvieta_iret);
    $vaicajums->execute();
    $vaicajums->bind_result($id_ipasnieks);
    $vaicajums->fetch();
    $vaicajums->close();

    if ($count > 0) {
        $_SESSION['pazinojumsMV'] = "Kļūda, veidojot nomas ierakstu!";
    } elseif ($id_ipasnieks == $lietotaja_id) {
        $_SESSION['pazinojumsMV'] = "Jūs nevarat izīrēt savu mājokli!";
    } elseif ($parklasanasSkaits > 0) {
        $_SESSION['pazinojumsMV'] = "Jūs jau izīrējat citu mājokli šajā periodā!";
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

header("Location: profils.php");
