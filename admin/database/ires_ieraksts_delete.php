<?php
require 'con_db.php';
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_iziresana WHERE iziresana_id = ?");
    $vaicajums->bind_param("i", $id);

    if ($vaicajums->execute()) {
        // echo "Pieteikums ir veiksmīgi izdzēsts!";
    } else {
        // echo "Kļūda: ".$savienojums->error;
    }

    $vaicajums->close();
    $savienojums->close();
}
