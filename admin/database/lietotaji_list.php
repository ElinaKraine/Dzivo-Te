<?php
require 'con_db.php';
session_start();

$lietotajaId = $_SESSION['lietotajaIdDt'];

if ($_SESSION['lietotajaLomaMV'] === 'Moderators') {
    $vaicajums = $savienojums->prepare("SELECT * FROM majuvieta_lietotaji WHERE statuss != 'Dzēsts' AND loma != 'Administrators' AND loma != 'Moderators' AND lietotaja_id != ?");
} elseif ($_SESSION['lietotajaLomaMV'] === 'Administrators') {
    $vaicajums = $savienojums->prepare("SELECT * FROM majuvieta_lietotaji WHERE statuss != 'Dzēsts' AND loma != 'Administrators' AND lietotaja_id != ?");
}

$vaicajums->bind_param('i', $lietotajaId);

if ($vaicajums->execute()) {
    $rezultats = $vaicajums->get_result();
    $json = array();

    while ($ieraksts = $rezultats->fetch_assoc()) {
        $json[] = array(
            'id' => htmlspecialchars($ieraksts['lietotaja_id']),
            'vards' => htmlspecialchars($ieraksts['vards']),
            'uzvards' => htmlspecialchars($ieraksts['uzvards']),
            'epasts' => htmlspecialchars($ieraksts['epasts']),
            'talrunis' => htmlspecialchars($ieraksts['talrunis']),
            'attels' => base64_encode($ieraksts['attels']),
            'loma' => htmlspecialchars($ieraksts['loma']),
            'izveidosanas_datums' => date("d.m.Y", strtotime($ieraksts['izveidosanas_datums'])),
        );
    }

    $jsonstring = json_encode($json);
    echo $jsonstring;
} else {
    // echo json_encode(array('error' => 'Kļūda: ' . $vaicajums->error));
}

$vaicajums->close();

$savienojums->close();
