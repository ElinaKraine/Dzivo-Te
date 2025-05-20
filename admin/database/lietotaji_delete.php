<?php
require 'con_db.php';
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $statuss = 'Dzēsts';

    $parbaudePirkt = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_pirkt WHERE id_ipasnieks = ?");
    $parbaudePirkt->bind_param("i", $id);
    $parbaudePirkt->execute();
    $parbaudePirkt->bind_result($countPirkt);
    $parbaudePirkt->fetch();
    $parbaudePirkt->close();

    $parbaudeIret = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_iret WHERE id_ipasnieks = ?");
    $parbaudeIret->bind_param("i", $id);
    $parbaudeIret->execute();
    $parbaudeIret->bind_result($countIret);
    $parbaudeIret->fetch();
    $parbaudeIret->close();

    if (($countPirkt > 0 && $countIret > 0) || ($countPirkt > 0) || ($countIret > 0)) {
        $_SESSION['pazinojumsMV'] = "Nevar dzēst! Šim lietotājam ir aktīvi sludinājumi.";
    } else {
        $vaicajums = $savienojums->prepare("UPDATE majuvieta_lietotaji SET statuss = ? WHERE lietotaja_id = ?");
        $vaicajums->bind_param("si", $statuss, $id);

        if ($vaicajums->execute()) {
            // echo "Veiksmīgi dzēsts!";
        } else {
            // echo "Kļūda: " . $savienojums->error;
        }

        $vaicajums->close();

        $parbaudePiet = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_pieteikumi WHERE id_majuvieta_pirkt = ?");
        $parbaudePiet->bind_param("i", $id);
        $parbaudePiet->execute();
        $parbaudePiet->bind_result($count);
        $parbaudePiet->fetch();
        $parbaudePiet->close();

        $vaicajumsPiet = $savienojums->prepare("DELETE FROM majuvieta_pieteikumi WHERE id_lietotajs = ?");
        $vaicajumsPiet->bind_param("i", $id);

        if ($vaicajumsPiet->execute()) {
            // echo "Veiksmīgi dzēst!";
        } else {
            // echo "Kļūda: ".$savienojums->error;
        }

        $vaicajumsPiet->close();
    }
    $savienojums->close();
}
