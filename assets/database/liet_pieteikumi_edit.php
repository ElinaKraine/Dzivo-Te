<?php
require '../../admin/database/con_db.php';

if (isset($_POST['id'])) {
    $statussValue = htmlspecialchars($_POST['statuss']);
    $id = intval($_POST['id']);
    $tagad = date("Y-m-d H:i:s");

    if (!empty($statussValue)) {
        $vaicajums = $savienojums->prepare("SELECT pt.id_majuvieta_pirkt, pi.statuss 
        FROM majuvieta_pieteikumi pt
        LEFT JOIN majuvieta_pirkt pi ON pt.id_majuvieta_pirkt = pi.pirkt_id
        WHERE pt.pieteikums_id = ?");
        $vaicajums->bind_param("i", $id);
        $vaicajums->execute();
        $vaicajums->bind_result($sludinajumsId, $sludinajumsStatuss);
        $vaicajums->fetch();
        $vaicajums->close();

        if ($sludinajumsStatuss === "Mājoklis ir iegādāts") {
            echo "Šo pieteikumu nevar rediģēt, jo mājoklis jau ir iegādāts.";
            $savienojums->close();
            exit;
        }

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

        $vaicajums = $savienojums->prepare("UPDATE majuvieta_pieteikumi SET statuss = ?, pedejais_izmainas_datums = ? WHERE pieteikums_id = ?");
        $vaicajums->bind_param("ssi", $statuss, $tagad, $id);
        if ($vaicajums->execute()) {
            echo "Pieteikums veiksmīgi rediģēts";
        } else {
            // echo "Kluda" . $savienojums->error;
        }
        $vaicajums->close();

        if ($statuss === "Mājoklis ir iegādāts") {
            $vaicajums = $savienojums->prepare("SELECT id_majuvieta_pirkt FROM majuvieta_pieteikumi WHERE pieteikums_id = ?");
            $vaicajums->bind_param("i", $id);
            $vaicajums->execute();
            $vaicajums->bind_result($sludinajumsId);
            $vaicajums->fetch();
            $vaicajums->close();

            if (!empty($sludinajumsId)) {
                $statussSludinajums = "Mājoklis ir iegādāts";
                $vaicajums = $savienojums->prepare("UPDATE majuvieta_pirkt SET statuss = ? WHERE pirkt_id = ?");
                $vaicajums->bind_param("si", $statussSludinajums, $sludinajumsId);
                $vaicajums->execute();
                $vaicajums->close();

                $vaicajums = $savienojums->prepare("UPDATE majuvieta_pieteikumi SET statuss = 'Atteikums', pedejais_izmainas_datums = ? WHERE id_majuvieta_pirkt = ? AND pieteikums_id != ?");
                $vaicajums->bind_param("sii", $tagad, $sludinajumsId, $id);
                $vaicajums->execute();
                $vaicajums->close();
            }
        }
    } else {
        echo "Visi ievadas lauki nav aizpildīti!";
    }

    $savienojums->close();
}
