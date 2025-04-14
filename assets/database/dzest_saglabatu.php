<?php
session_start();
header('Content-Type: application/json');
require '../../admin/database/con_db.php';

if (!isset($_SESSION['lietotajaIdDt'])) {
    echo json_encode(["success" => false, "message" => "unauthorized"]);
    exit;
}

if (!isset($_POST["id_sludinajums"], $_POST["veids"], $_POST["tips"])) {
    echo json_encode(["success" => false, "message" => "Kaut kas trūkst!"]);
    exit;
}

$lietotaja_id = $_SESSION['lietotajaIdDt'];
$id_sludinajums = intval($_POST['id_sludinajums']);
$sludinajuma_veids = $_POST['veids'] === 'Iret' ? 'Iret' : 'Pirkt';
$majokla_tips = $_POST['tips'] === 'Maja' ? 'Maja' : 'Dzivoklis';

$vaicajums = $savienojums->prepare("
    DELETE FROM dzivote_saglabatie 
    WHERE id_lietotajs = ? AND id_sludinajums = ? AND sludinajuma_veids = ? AND majokla_tips = ?
");
$vaicajums->bind_param("iiss", $lietotaja_id, $id_sludinajums, $sludinajuma_veids, $majokla_tips);

if ($vaicajums->execute()) {
    echo json_encode(["success" => true, "message" => "Izņemts no saglabātajiem"]);
} else {
    echo json_encode(["success" => false, "message" => "Kļūda dzēšot"]);
}

$vaicajums->close();
$savienojums->close();
