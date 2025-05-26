<?php
require '../../admin/database/con_db.php';

if (isset($_POST['id'])) {
    $statussValue = htmlspecialchars($_POST['statuss']);
    $id = intval($_POST['id']);
    $tagad = date("Y-m-d H:i:s");
    $statuss = "";
    switch ($statussValue) {
        case 'iesniegtsPieteikums':
            $statuss = "Iesniegts pieteikums";
            break;
        case 'pieteikumaParskatisana':
            $statuss = "Pieteikuma pārskatīšana";
            break;
        case 'majoklaIegadesProcesa':
            $statuss = "Mājokļa iegādes procesā";
            break;
        case 'majoklisIrIegadats':
            $statuss = "Mājoklis ir iegādāts";
            break;
        case 'atteikums':
            $statuss = "Atteikums";
            break;
    }

    $sqlTeikums = "UPDATE majuvieta_pieteikumi SET statuss = ?, pedejais_izmainas_datums = ? WHERE pieteikums_id = ?";
    $vaicajums = $savienojums->prepare($sqlTeikums);
    $vaicajums->bind_param("ssi", $statuss, $tagad, $id);


    if ($vaicajums->execute()) {
        echo "Pieteikums veiksmīgi rediģēts";
    } else {
        // echo "Kluda" . $savienojums->error;
    }
    $savienojums->close();
}
