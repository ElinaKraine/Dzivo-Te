<?php
require '../../admin/database/con_db.php';
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_pieteikumi WHERE pieteikums_id = ?");
    $vaicajums->bind_param("i", $id);

    if ($vaicajums->execute()) {
        // echo "Veiksmīgi dzēst!";
    } else {
        // echo "Kļūda: ".$savienojums->error;
    }

    $vaicajums->close();
    $savienojums->close();
}
