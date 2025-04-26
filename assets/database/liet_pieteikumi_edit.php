<?php
require '../../admin/database/con_db.php';

if (isset($_POST['id'])) {
    $statussValue = htmlspecialchars($_POST['statuss']);
    $id = intval($_POST['id']);
    $current_time = date("Y-m-d H:i:s");
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

    $sql = "UPDATE majuvieta_pieteikumi SET statuss = ?, pedejais_izmainas_datums = ? WHERE pieteikums_id = ?";
    $vaicajums = $savienojums->prepare($sql);
    $vaicajums->bind_param("ssi", $statuss, $current_time, $id);


    if ($vaicajums->execute()) {
        echo "Veiksmigi redigets";
    } else {
        echo "Kluda" . $savienojums->error;
    }
    $savienojums->close();
}
