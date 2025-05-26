<?php
require '../../admin/database/con_db.php';
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $statuss = 'Dzēsts';
    $statussPiet = 'Atteikums';

    if ($_POST['tabula'] == 'Pirkt') {
        $vaicajums = $savienojums->prepare("UPDATE majuvieta_pirkt SET statuss = ? WHERE pirkt_id = ?");
        $vaicajums->bind_param("si", $statuss, $id);
        if ($vaicajums->execute()) {
            // echo "Veiksmīgi dzēst!";
        } else {
            // echo "Kļūda: ".$savienojums->error;
        }
        $vaicajums->close();

        $vaicajums = $savienojums->prepare("UPDATE majuvieta_pieteikumi SET statuss = ? WHERE id_majuvieta_pirkt = ?");
        $vaicajums->bind_param("si", $statussPiet, $id);
        if ($vaicajums->execute()) {
            // echo "Veiksmīgi dzēst!";
        } else {
            // echo "Kļūda: ".$savienojums->error;
        }
        $vaicajums->close();
    } elseif ($_POST['tabula'] == 'Iret') {
        $tagad = date("Y-m-d");

        $vaicajums = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_iziresana WHERE id_majuvieta_iret = ? AND izrakstisanas_datums >= ?");
        $vaicajums->bind_param("is", $id, $tagad);
        $vaicajums->execute();
        $vaicajums->bind_result($count);
        $vaicajums->fetch();
        $vaicajums->close();

        if ($count > 0) {
            echo "Nevar dzēst! Šim sludinājumam ir aktīvas vai nākotnes īres saistības.";
        } else {
            $vaicajums = $savienojums->prepare("UPDATE majuvieta_iret SET statuss = ? WHERE iret_id = ?");
            $vaicajums->bind_param("si", $statuss, $id);
            if ($vaicajums->execute()) {
                // echo "Veiksmīgi dzēsts!";
            } else {
                // echo "Kļūda: " . $savienojums->error;
            }
            $vaicajums->close();
        }
    }

    $savienojums->close();
}
