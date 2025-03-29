<?php
session_start();
header('Content-Type: application/json');
require '../../admin/database/con_db.php';

if (!isset($_SESSION['lietotajaIdDt'])) {
    echo json_encode(["success" => false, "message" => "Lietotājs nav pieslēdzies"]);
    exit;
}

if (!isset($_POST["id_sludinajums"])) {
    echo json_encode(["success" => false, "message" => "Trūkst ID"]);
    exit;
}

$lietotaja_id = $_SESSION['lietotajaIdDt'];
$id_sludinajums = intval($_POST['id_sludinajums']);

$vaicajums = $savienojums->prepare("
    DELETE FROM dzivote_saglabatie 
    WHERE id_lietotajs = ? AND id_sludinajums = ?
");
$vaicajums->bind_param("ii", $lietotaja_id, $id_sludinajums);

if ($vaicajums->execute()) {
    echo json_encode(["success" => true, "message" => "Izņemts no saglabātajiem"]);
} else {
    echo json_encode(["success" => false, "message" => "Kļūda dzēšot"]);
}

$vaicajums->close();
$savienojums->close();
