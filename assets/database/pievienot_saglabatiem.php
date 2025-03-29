<?php
session_start();
header('Content-Type: application/json');

require '../../admin/database/con_db.php';
if (isset($_POST["id_sludinajums"])) {
    if (!isset($_SESSION['lietotajaIdDt'])) {
        echo json_encode(["success" => false, "message" => "Lietotājs nav pieslēdzies"]);
        exit;
    }

    $lietotaja_id = $_SESSION['lietotajaIdDt'];
    $id_sludinajums = intval($_POST['id_sludinajums']);

    $parbaudijums = $savienojums->prepare("
        SELECT COUNT(*) 
        FROM dzivote_saglabatie 
        WHERE id_lietotajs = ? AND id_sludinajums = ?
    ");
    $parbaudijums->bind_param("ii", $lietotaja_id, $id_sludinajums);
    $parbaudijums->execute();
    $parbaudijums->bind_result($count);
    $parbaudijums->fetch();
    $parbaudijums->close();

    if ($count > 0) {
        echo json_encode(["success" => false, "message" => "Jau saglabāts"]);
        exit;
    }

    $vaicajums = $savienojums->prepare("
        INSERT INTO dzivote_saglabatie (id_lietotajs, id_sludinajums) 
        VALUES (?, ?)
    ");

    $vaicajums->bind_param("ii", $lietotaja_id, $id_sludinajums);

    if ($vaicajums->execute()) {
        echo json_encode(["success" => true, "message" => "Saglabāts"]);
    } else {
        echo json_encode(["success" => false, "message" => "Kļūda saglabājot"]);
    }

    $vaicajums->close();
    $savienojums->close();
    exit;
}

echo json_encode(["success" => false, "message" => "Nederīgs pieprasījums"]);
exit;
