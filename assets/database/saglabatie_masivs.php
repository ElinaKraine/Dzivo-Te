<?php
session_start();
// header('Content-Type: application/json');

require '../../admin/database/con_db.php';

if (!isset($_SESSION['lietotajaIdDt'])) {
    echo json_encode([]);
    exit;
} else {
    $lietotaja_id = $_SESSION['lietotajaIdDt'];
    // $veids = isset($_GET['veids']) && $_GET['veids'] === 'Iret' ? 'Iret' : 'Pirkt';
    $veids = isset($_GET['veids']);

    $rezultats = $savienojums->prepare("
        SELECT id_sludinajums 
        FROM dzivote_saglabatie 
        WHERE id_lietotajs = ? AND sludinajuma_veids = ?
    ");
    $rezultats->bind_param("is", $lietotaja_id, $veids);
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
