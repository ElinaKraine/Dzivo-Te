<?php
require '../../admin/database/con_db.php';
session_start();

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($_SESSION['lietotajaLomaMV'] === 'Lietotājs') {
        $vaicajums = $savienojums->prepare("SELECT statuss FROM majuvieta_pieteikumi WHERE pieteikums_id = ?");
        $vaicajums->bind_param("i", $id);
        $vaicajums->execute();
        $vaicajums->bind_result($esosajaisStatus);
        $vaicajums->fetch();
        $vaicajums->close();

        if ($esosajaisStatus === "Mājoklis ir iegādāts") {
            echo "Šo pieteikumu nevar dzēst, jo mājoklis jau ir iegādāts.";
            $savienojums->close();
            exit;
        }
    }

    $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_pieteikumi WHERE pieteikums_id = ?");
    $vaicajums->bind_param("i", $id);

    if ($vaicajums->execute()) {
        // echo "Pieteikums ir veiksmīgi izdzēsts!";
    } else {
        // echo "Kļūda: ".$savienojums->error;
    }

    $vaicajums->close();
    $savienojums->close();
}
