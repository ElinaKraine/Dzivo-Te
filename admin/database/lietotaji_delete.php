<?php
require 'con_db.php';
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $statuss = 'Dzēsts';
    $tagad = date("Y-m-d");

    $parbaudePirkt = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_pirkt WHERE id_ipasnieks = ? AND (statuss = 'Apsiprināts | Publicēts' OR statuss = 'Mājoklis ir iegādāts')");
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

    if (($countPirkt > 0 && $countIret > 0) || ($countPirkt > 0) || ($countIret > 0)) {
        echo "Nevar dzēst! Šim lietotājam ir sludinājumi.";
    } else {
        $vaicajums = $savienojums->prepare("UPDATE majuvieta_lietotaji SET statuss = ? WHERE lietotaja_id = ?");
        $vaicajums->bind_param("si", $statuss, $id);

        if ($vaicajums->execute()) {
            // echo "Veiksmīgi dzēsts!";
        } else {
            // echo "Kļūda: " . $savienojums->error;
        }

        $vaicajums->close();

        $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_pieteikumi WHERE id_lietotajs = ?");
        $vaicajums->bind_param("i", $id);

        if ($vaicajums->execute()) {
            // echo "Veiksmīgi dzēst!";
        } else {
            // echo "Kļūda: " . $savienojums->error;
        }
        $vaicajums->close();

        $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_iziresana WHERE id_lietotajs = ? AND izrakstisanas_datums >= ?");
        $vaicajums->bind_param("is", $id, $tagad);

        if ($vaicajums->execute()) {
            // echo "Veiksmīgi dzēst!";
        } else {
            // echo "Kļūda: " . $savienojums->error;
        }
        $vaicajums->close();
    }
    $savienojums->close();
}
