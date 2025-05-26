<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nosutit"])) {
    require "admin/database/con_db.php";

    $lietotaja_id = $_SESSION['lietotajaIdDt'];
    $ip_adrese = $_SERVER['REMOTE_ADDR'];


    $id_majuvieta_pirkt = intval($_POST['id_majuvieta_pirkt']);

    $parbaude = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_pieteikumi WHERE id_lietotajs = ? AND id_majuvieta_pirkt = ?");
    $parbaude->bind_param("ii", $lietotaja_id, $id_majuvieta_pirkt);
    $parbaude->execute();
    $parbaude->bind_result($count);
    $parbaude->fetch();
    $parbaude->close();

    $vaicajums = $savienojums->prepare("SELECT id_ipasnieks FROM majuvieta_pirkt WHERE pirkt_id = ?");
    $vaicajums->bind_param("i", $id_majuvieta_pirkt);
    $vaicajums->execute();
    $vaicajums->bind_result($id_ipasnieks);
    $vaicajums->fetch();
    $vaicajums->close();

    if ($count > 0) {
        $_SESSION['pazinojumsMV'] = "Jūs jau esat nosūtījis pieteikumu par šo māju!";
    } elseif ($id_ipasnieks == $lietotaja_id) {
        $_SESSION['pazinojumsMV'] = "Jūs nevarat pieteikties uz savu sludinājumu!";
    } else {
        $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_pieteikumi (id_lietotajs, id_majuvieta_pirkt, statuss, ip_adrese) VALUES (?, ?, default, ?)");
        $vaicajums->bind_param("iis", $lietotaja_id, $id_majuvieta_pirkt, $ip_adrese);
        if ($vaicajums->execute()) {
            $_SESSION['pazinojumsMV'] = "Pieteikums veiksmīgi nosutīts!";
        } else {
            $_SESSION['pazinojumsMV'] = "Kļūda sistemā!";
        }
        $vaicajums->close();
        $savienojums->close();
    }
}

header("Location: profils.php");
