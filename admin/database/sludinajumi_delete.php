<?php
require 'con_db.php';
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $veids = $_POST['tabula'];

    if ($veids == 'Pirkt') {
        $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_pirkt WHERE pirkt_id = ?");
        $vaicajums->bind_param("i", $id);

        if ($vaicajums->execute()) {
            echo "Veiksmīgi dzēst pirkt!";
        } else {
            echo "Kļūda: " . $savienojums->error;
        }

        $vaicajums->close();

        $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_pieteikumi WHERE id_majuvieta_pirkt = ?");
        $vaicajums->bind_param("i", $id);

        if ($vaicajums->execute()) {
            echo "Veiksmīgi dzēst pieteikums!";
        } else {
            echo "Kļūda: " . $savienojums->error;
        }

        $vaicajums->close();
    } elseif ($veids == 'Iret') {
        $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_iret WHERE iret_id = ?");
        $vaicajums->bind_param("i", $id);

        if ($vaicajums->execute()) {
            echo "Veiksmīgi dzēsts iret!";
        } else {
            echo "Kļūda: " . $savienojums->error;
        }

        $vaicajums->close();

        // Īres ierakstu dzēšana
        $tagad = date("Y-m-d");
        $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_iziresana WHERE id_majuvieta_iret = ? AND izrakstisanas_datums >= ?");
        $vaicajums->bind_param("is", $id, $tagad);

        if ($vaicajums->execute()) {
            // echo "Veiksmīgi dzēst ires ieraksts!";
        } else {
            // echo "Kļūda: " . $savienojums->error;
        }

        $vaicajums->close();
    }

    // Adrešu dzēšana
    $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_adrese WHERE sludinajuma_veids = ? AND id_sludinajums = ?");
    $vaicajums->bind_param("si", $veids, $id);
    if ($vaicajums->execute()) {
        echo "Veiksmīgi dzēst adrese!";
    } else {
        echo "Kļūda: " . $savienojums->error;
    }
    $vaicajums->close();

    // Attēlu atjaunināšana
    $vaicajums = $savienojums->prepare("DELETE FROM majuvieta_atteli WHERE sludinajuma_veids = ? AND id_sludinajums = ?");
    $vaicajums->bind_param("si", $veids, $id);
    if ($vaicajums->execute()) {
        echo "Veiksmīgi dzēst atteli!";
    } else {
        echo "Kļūda: " . $savienojums->error;
    }
    $vaicajums->close();

    $savienojums->close();
}
