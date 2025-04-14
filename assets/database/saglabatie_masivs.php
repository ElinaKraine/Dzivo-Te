<?php
session_start();

require '../../admin/database/con_db.php';

if (!isset($_SESSION['lietotajaIdDt'])) {
    echo json_encode([]);
    exit;
} else {
    $lietotaja_id = $_SESSION['lietotajaIdDt'];
    $veids = isset($_GET['veids']) && $_GET['veids'] === 'Iret' ? 'Iret' : 'Pirkt';
    $majokla_tips = $_GET['tips'] === 'Maja' ? 'Maja' : 'Dzivoklis';

    $rezultats = $savienojums->prepare("
        SELECT id_sludinajums 
        FROM dzivote_saglabatie 
        WHERE id_lietotajs = ? AND sludinajuma_veids = ? AND  majokla_tips = ?
    ");
    $rezultats->bind_param("iss", $lietotaja_id, $veids, $majokla_tips);
    $rezultats->execute();
    $rezultats->bind_result($id);
    $saglabatie = [];

    while ($rezultats->fetch()) {
        $saglabatie[] = $id;
    }

    $rezultats->close();
    $savienojums->close();

    echo json_encode($saglabatie);
}
