<?php
session_start();
header('Content-Type: application/json');
require '../../admin/database/con_db.php';

$response = [];

if (isset($_SESSION['lietotajaIdDt'])) {
    if (isset($_POST["id_sludinajums"], $_POST["veids"])) {
        $lietotaja_id = $_SESSION['lietotajaIdDt'];
        $id_sludinajums = intval($_POST['id_sludinajums']);
        $sludinajuma_veids = $_POST['veids'];
        $majokla_tips = $_POST['tips'];

        $parbaude = $savienojums->prepare("SELECT COUNT(*) 
                                                FROM dzivote_saglabatie 
                                                WHERE id_lietotajs = ? AND id_sludinajums = ?
                                                AND sludinajuma_veids = ? AND majokla_tips = ?
                                            ");
        $parbaude->bind_param("iiss", $lietotaja_id, $id_sludinajums, $sludinajuma_veids, $majokla_tips);
        $parbaude->execute();
        $parbaude->bind_result($count);
        $parbaude->fetch();
        $parbaude->close();

        if ($count > 0) {
            echo json_encode(["success" => false, "message" => "Jau saglabāts"]);
            exit;
        }

        $vaicajums = $savienojums->prepare("INSERT INTO dzivote_saglabatie (id_lietotajs, id_sludinajums, sludinajuma_veids, majokla_tips) VALUES (?, ?, ?, ?)");
        $vaicajums->bind_param("iiss", $lietotaja_id, $id_sludinajums, $sludinajuma_veids, $majokla_tips);

        if ($vaicajums->execute()) {
            $response = ["success" => true];
        } else {
            $response = ["success" => false, "message" => "Kļūda: " . $savienojums->error];
        }

        $vaicajums->close();
    } else {
        $response = ["success" => false, "message" => "Kaut kas trūkst!"];
    }
} else {
    $response = ["success" => false, "message" => "unauthorized"];
}

$savienojums->close();
echo json_encode($response);
