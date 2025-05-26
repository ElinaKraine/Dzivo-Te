<?php
require 'con_db.php';
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $statuss = 'Dzēsts';

    $parbaudePirkt = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_pirkt WHERE id_ipasnieks = ? AND statuss = 'Apsiprināts | Publicēts'");
    $parbaudePirkt->bind_param("i", $id);
    $parbaudePirkt->execute();
    $parbaudePirkt->bind_result($countPirkt);
    $parbaudePirkt->fetch();
    $parbaudePirkt->close();

    $parbaudeIret = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_iret WHERE id_ipasnieks = ? AND statuss = 'Apsiprināts | Publicēts'");
    $parbaudeIret->bind_param("i", $id);
    $parbaudeIret->execute();
    $parbaudeIret->bind_result($countIret);
    $parbaudeIret->fetch();
    $parbaudeIret->close();

    $current_date = date("Y-m-d");

    if (($countPirkt > 0 && $countIret > 0) || ($countPirkt > 0) || ($countIret > 0)) {
        $_SESSION['pazinojumsMV'] = "Nevar dzēst! Šim lietotājam ir aktīvi sludinājumi.";
    } else {
        $vaicajums = $savienojums->prepare("UPDATE majuvieta_lietotaji SET statuss = ? WHERE lietotaja_id = ?");
        $vaicajums->bind_param("si", $statuss, $id);

        if ($vaicajums->execute()) {
            echo "Veiksmīgi dzēsts!";
        } else {
            echo "Kļūda: " . $savienojums->error;
        }

        $vaicajums->close();

        $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_pieteikumi WHERE id_lietotajs = ?");
        $vaicajums->bind_param("i", $id);

        if ($vaicajums->execute()) {
            echo "Veiksmīgi dzēst!";
        } else {
            echo "Kļūda: " . $savienojums->error;
        }
        $vaicajums->close();

        $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_iziresana WHERE id_lietotajs = ? AND izrakstisanas_datums >= ?");
        $vaicajums->bind_param("is", $id, $current_date);

        if ($vaicajums->execute()) {
            echo "Veiksmīgi dzēst!";
        } else {
            echo "Kļūda: " . $savienojums->error;
        }
        $vaicajums->close();
    }
    $savienojums->close();
}
