<?php
session_start();
header('Content-Type: application/json');

require '../../admin/database/con_db.php';

if (!isset($_SESSION['lietotajaIdDt'])) {
    echo json_encode([]);
    exit;
}

$lietotaja_id = $_SESSION['lietotajaIdDt'];

$rezultats = $savienojums->prepare("
    SELECT id_sludinajums 
    FROM dzivote_saglabatie 
    WHERE id_lietotajs = ?
");
$rezultats->bind_param("i", $lietotaja_id);
$rezultats->execute();
$rezultats->bind_result($id);
$saglabatie = [];

while ($rezultats->fetch()) {
    $saglabatie[] = $id;
}

echo json_encode($saglabatie);
