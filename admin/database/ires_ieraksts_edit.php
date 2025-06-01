<?php
require 'con_db.php';

if (isset($_POST['id'])) {
    $cena = htmlspecialchars($_POST['cena']);
    $registresanas_datums = $_POST['registresanas_datums'];
    $izrakstisanas_datums = $_POST['izrakstisanas_datums'];
    $id = intval($_POST['id']);
    $tagad = date("Y-m-d H:i:s");

    if (!empty($cena) && !empty($registresanas_datums) && !empty($izrakstisanas_datums)) {
        //region Parbaude
        if ($cena <= 0) {
            echo "Cenai jābūt lielākai par 0!";
            exit;
        }

        $dateStart = strtotime($registresanas_datums);
        $dateEnd = strtotime($izrakstisanas_datums);
        if (!$dateStart || !$dateEnd || $dateEnd <= $dateStart) {
            echo "Datumu intervāls nav korekts (vismaz viena diena)!";
            exit;
        }

        $vaicajums = $savienojums->prepare(
            "SELECT id_majuvieta_iret 
             FROM majuvieta_iziresana 
             WHERE iziresana_id = ?"
        );
        $vaicajums->bind_param("i", $id);
        $vaicajums->execute();
        $rez = $vaicajums->get_result();
        $vaicajums->close();

        if ($rez->num_rows === 0) {
            echo "Nav atrasts īres ieraksts!";
            exit;
        }

        $ieraksts = $rez->fetch_assoc();
        $id_majuvieta_iret = $ieraksts['id_majuvieta_iret'];

        $vaicajums = $savienojums->prepare(
            "SELECT 1 FROM majuvieta_iziresana 
             WHERE id_majuvieta_iret = ? 
             AND iziresana_id != ?
             AND (
                (? BETWEEN registresanas_datums AND izrakstisanas_datums) OR
                (? BETWEEN registresanas_datums AND izrakstisanas_datums) OR
                (registresanas_datums BETWEEN ? AND ?) OR
                (izrakstisanas_datums BETWEEN ? AND ?)
             )"
        );
        $vaicajums->bind_param(
            "iissssss",
            $id_majuvieta_iret,
            $id,
            $registresanas_datums,
            $izrakstisanas_datums,
            $registresanas_datums,
            $izrakstisanas_datums,
            $registresanas_datums,
            $izrakstisanas_datums
        );
        $vaicajums->execute();
        $vaicajums->store_result();

        if ($vaicajums->num_rows > 0) {
            echo "Izvēlētais īres periods pārklājas ar esošu ierakstu!";
            exit;
        }
        $vaicajums->close();
        //endregion

        $vaicajums = $savienojums->prepare("UPDATE majuvieta_iziresana SET registresanas_datums = ?, izrakstisanas_datums = ?, cena = ?, atjauninasanas_datums = ? WHERE iziresana_id = ?");
        $vaicajums->bind_param("ssdsi", $registresanas_datums, $izrakstisanas_datums, $cena, $tagad, $id);

        if ($vaicajums->execute()) {
            echo "Īres ieraksts veiksmīgi rediģēts";
        } else {
            // echo "Kluda" . $savienojums->error;
        }
        $vaicajums->close();
    } else {
        echo "Visi ievadas lauki nav aizpildīti!";
    }
}
$savienojums->close();
