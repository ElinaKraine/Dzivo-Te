<?php
require '../../admin/database/con_db.php';
if (isset($_POST['id'])) {
    $id = htmlspecialchars($_POST['id']);
    $veids = "Pirkt";

    $vaicajums = $savienojums->prepare(
        "SELECT 
                mp.pieteikums_id AS id,
                mp.statuss,
                mp.izveidosanas_datums,
                mv.cena,
                mv.majokla_tips,
                CONCAT(ad.pilseta, ' ', ad.iela, ' ', ad.majas_numurs) AS adrese,
                ml.epasts,
                mp.pedejais_izmainas_datums,
                mp.ip_adrese
            FROM majuvieta_pieteikumi mp
            JOIN majuvieta_pirkt mv ON mp.id_majuvieta_pirkt = mv.pirkt_id
            INNER JOIN majuvieta_adrese ad ON mv.pirkt_id = ad.id_sludinajums
            JOIN majuvieta_lietotaji ml ON mp.id_lietotajs = ml.lietotaja_id 
            WHERE ad.sludinajuma_veids = ? AND mp.pieteikums_id = ?"
    );
    $vaicajums->bind_param('si', $veids, $id);
    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();
    if (!$rezultats) {
        // die('Kļūda: ' . $savienojums->error);
    }

    while ($ieraksts = $rezultats->fetch_assoc()) {
        $statuss = "";
        switch (htmlspecialchars($ieraksts['statuss'])) {
            case 'Iesniegts pieteikums':
                $statuss = "iesniegtsPieteikums";
                break;
            case 'Pieteikuma pārskatīšana':
                $statuss = "pieteikumaParskatisana";
                break;
            case 'Mājokļa iegādes procesā':
                $statuss = "majoklaIegadesProcesa";
                break;
            case 'Mājoklis ir iegādāts':
                $statuss = "majoklisIrIegadats";
                break;
            case 'Atteikums':
                $statuss = "atteikums";
                break;
        }

        $json[] = array(
            'id' => htmlspecialchars($ieraksts['id']),
            'epasts' => htmlspecialchars($ieraksts['epasts']),
            'statuss' => $statuss,
            'izveidosanas_datums' => date("d.m.Y", strtotime($ieraksts['izveidosanas_datums'])),
            'cena' => htmlspecialchars($ieraksts['cena']),
            'majokla_tips' => htmlspecialchars($ieraksts['majokla_tips']),
            'adrese' => htmlspecialchars($ieraksts['adrese']),
            'ip_adrese' => htmlspecialchars($ieraksts['ip_adrese']),
            'atjauninasanas_datums' => date("d.m.Y H:i", strtotime($ieraksts['pedejais_izmainas_datums'])),
        );
    }

    $vaicajums->close();
    $savienojums->close();

    $jsonstring = json_encode($json[0]);
    echo $jsonstring;
}
