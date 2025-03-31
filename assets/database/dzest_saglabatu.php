<?php
session_start();
header('Content-Type: application/json');
require '../../admin/database/con_db.php';

if (!isset($_SESSION['lietotajaIdDt'])) {
    echo json_encode(["success" => false, "message" => "unauthorized"]);
    exit;
}

if (!isset($_POST["id_sludinajums"], $_POST["veids"])) {
    echo json_encode(["success" => false, "message" => "Trūkst ID vai veids"]);
    exit;
}

$lietotaja_id = $_SESSION['lietotajaIdDt'];
$id_sludinajums = intval($_POST['id_sludinajums']);
$sludinajuma_veids = $_POST['veids'] === 'Iret' ? 'Iret' : 'Pirkt';

$vaicajums = $savienojums->prepare("
    DELETE FROM dzivote_saglabatie 
    WHERE id_lietotajs = ? AND id_sludinajums = ? AND sludinajuma_veids = ?
");
$vaicajums->bind_param("iis", $lietotaja_id, $id_sludinajums, $sludinajuma_veids);

if ($vaicajums->execute()) {
    echo json_encode(["success" => true, "message" => "Izņemts no saglabātajiem"]);
} else {
    echo json_encode(["success" => false, "message" => "Kļūda dzēšot"]);
}

$vaicajums->close();
$savienojums->close();
