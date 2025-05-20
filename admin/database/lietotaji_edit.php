<?php
require 'con_db.php';

if (isset($_POST['lietotajs_admin_ID'])) {
    $vards = htmlspecialchars($_POST['lietVardsTabulaAdmin']);
    $uzvards = htmlspecialchars($_POST['lietUzvardsTabulaAdmin']);
    $epasts = htmlspecialchars($_POST['lietEpastsTabulaAdmin']);
    $talrunis = htmlspecialchars($_POST['lietTalrunisTabulaAdmin']);
    $loma = htmlspecialchars($_POST['lomaSelect']);
    $id = intval($_POST['lietotajs_admin_ID']);

    $nomainitAttelu = $_POST['nomainitAtteluTabulaAdmin'] ?? 'ne';
    $nomainitParole = $_POST['nomainitParoleTabulaAdmin'] ?? 'ne';

    $current_time = date("Y-m-d H:i:s");

    $attels_data = null;
    $paroleHash = null;

    if ($nomainitAttelu === "ja") {
        if (isset($_FILES['attelsTabulaAdmin']) && $_FILES['attelsTabulaAdmin']['error'] === UPLOAD_ERR_OK) {
            $attels_tmp = $_FILES['attelsTabulaAdmin']['tmp_name'];
            $attels_data = file_get_contents($attels_tmp);
        }
    }

    if ($nomainitParole === "ja") {
        if (!empty($_POST['lietParoleTabulaAdmin']) && !empty($_POST['lietParoleOtraisTabulaAdmin'])) {
            if ($_POST['lietParoleTabulaAdmin'] !== $_POST['lietParoleOtraisTabulaAdmin']) {
                echo "Paroles nesakrīt!";
            } else {
                $paroleHash = password_hash($_POST['lietParoleTabulaAdmin'], PASSWORD_DEFAULT);
            }
        }
    }

    $sql_teikums = "UPDATE majuvieta_lietotaji SET vards = ?, uzvards = ?, epasts = ?, talrunis = ?, loma = ?, ";
    $params = [$vards, $uzvards, $epasts, $talrunis, $loma];
    $types = "sssss";

    if ($attels_data !== null) {
        $sql_teikums .= "attels = ?, ";
        $params[] = $attels_data;
        $types .= "s";
    }

    if ($paroleHash !== null) {
        $sql_teikums .= "parole = ?, ";
        $params[] = $paroleHash;
        $types .= "s";
    }

    $sql_teikums .= "atjauninasanas_datums = ? WHERE lietotaja_id = ?";
    $params[] = $current_time;
    $params[] = $id;
    $types .= "si";

    $vaicajums = $savienojums->prepare($sql_teikums);
    $vaicajums->bind_param($types, ...$params);

    if ($vaicajums->execute()) {
        echo "Veiksmīgi rediģēts";
    } else {
        // echo "Kļūda: " . $savienojums->error;
    }
    $savienojums->close();
} else {
    echo "ID nav saņemts";
}
