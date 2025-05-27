<?php
session_start();
require '../../admin/database/con_db.php';

if (isset($_SESSION['lietotajaIdDt'])) {
    $lietotaja_id = $_SESSION['lietotajaIdDt'];
    $veids = isset($_GET['veids']) && $_GET['veids'] === 'Iret' ? 'Iret' : 'Pirkt';
    $majokla_tips = $_GET['tips'] === 'Maja' ? 'Maja' : 'Dzivoklis';

    $vaicajums = $savienojums->prepare("SELECT id_sludinajums 
                                        FROM dzivote_saglabatie 
                                        WHERE id_lietotajs = ? AND sludinajuma_veids = ?
                                        AND  majokla_tips = ?
                                    ");
    $vaicajums->bind_param("iss", $lietotaja_id, $veids, $majokla_tips);
    $vaicajums->execute();
    $vaicajums->bind_result($id);
    $saglabatie = [];

    while ($vaicajums->fetch()) {
        $saglabatie[] = $id;
    }

    $vaicajums->close();
} else {
    echo json_encode([]);
    exit;
}

$savienojums->close();
echo json_encode($saglabatie);
