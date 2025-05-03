<?php
require '../../admin/database/con_db.php';


if (isset($_POST['liet_ID'])) {
    $vards = htmlspecialchars($_POST['lietVards']);
    $uzvards = htmlspecialchars($_POST['lietUzvards']);
    $epasts = htmlspecialchars($_POST['lietEpasts']);
    $talrunis = htmlspecialchars($_POST['lietTalrunis']);
    $id = intval($_POST['liet_ID']);
    $current_time = date("Y-m-d H:i:s");

    $nomainitAttelu = $_POST['nomainitAttelu'] ?? 'ne';
    $nomainitParole = $_POST['nomainitParole'] ?? 'ne';

    $attels_data = null;
    $paroleHash = null;

    if ($nomainitAttelu === "ja" && isset($_FILES['attels']) && $_FILES['attels']['error'] === UPLOAD_ERR_OK) {
        $attels_tmp = $_FILES['attels']['tmp_name'];
        $attels_data = file_get_contents($attels_tmp);
    }

    if ($nomainitParole === "ja" && !empty($_POST['lietParole']) && !empty($_POST['lietParoleOtrais'])) {
        if ($_POST['lietParole'] !== $_POST['lietParoleOtrais']) {
            echo "Paroles nesakrīt!";
        } else {
            $paroleHash = password_hash($_POST['lietParole'], PASSWORD_DEFAULT);
        }
    }

    $sql = "UPDATE majuvieta_lietotaji SET vards = ?, uzvards = ?, epasts = ?, talrunis = ?, ";
    $params = [$vards, $uzvards, $epasts, $talrunis];
    $types = "ssss";

    if ($attels_data !== null) {
        $sql .= "attels = ?, ";
        $params[] = $attels_data;
        $types .= "s";
    }

    if ($paroleHash !== null) {
        $sql .= "parole = ?, ";
        $params[] = $paroleHash;
        $types .= "s";
    }

    $sql .= "atjauninasanas_datums = ? WHERE lietotaja_id = ?";
    $params[] = $current_time;
    $params[] = $id;
    $types .= "si";

    $vaicajums = $savienojums->prepare($sql);
    $vaicajums->bind_param($types, ...$params);

    if ($vaicajums->execute()) {
        // echo "Veiksmīgi rediģēts";
    } else {
        // echo "Kļūda: " . $savienojums->error;
    }
    $savienojums->close();
} else {
    // echo "ID nav saņemts";
    // print_r($_POST);
}
