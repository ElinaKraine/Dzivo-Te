<?php
require '../../admin/database/con_db.php';

$vaicajums = $savienojums->prepare(
    "SELECT 
        mp.pieteikums_id AS id,
        mp.statuss,
        mp.izveidosanas_datums,
        mv.cena,
        mv.majokla_tips,
        CONCAT(ad.pilseta, ' ', ad.iela, ' ', ad.majas_numurs) AS adrese,
        ml.epasts
    FROM majuvieta_pieteikumi mp
    JOIN majuvieta_pirkt mv ON mp.id_majuvieta_pirkt = mv.pirkt_id
    INNER JOIN majuvieta_adrese ad ON mv.pirkt_id = ad.id_sludinajums
    JOIN majuvieta_lietotaji ml ON mp.id_lietotajs = ml.lietotaja_id 
    WHERE ad.sludinajuma_veids = 'Pirkt'
    ORDER BY mp.izveidosanas_datums DESC"
);

if ($vaicajums->execute()) {
    $rezultats = $vaicajums->get_result();
    $json = array();

    while ($ieraksts = $rezultats->fetch_assoc()) {
        $json[] = array(
            'id' => htmlspecialchars($ieraksts['id']),
            'izveidosanas_datums' => date("d.m.Y", strtotime($ieraksts['izveidosanas_datums'])),
            'statuss' => htmlspecialchars($ieraksts['statuss']),
            'majokla_tips' => htmlspecialchars($ieraksts['majokla_tips']),
            'adrese' => htmlspecialchars($ieraksts['adrese']),
            'cena' => htmlspecialchars($ieraksts['cena']),
            'epasts' => htmlspecialchars($ieraksts['epasts']),
        );
    }

    $jsonstring = json_encode($json);
    echo $jsonstring;
} else {
    // echo json_encode(array('error' => 'Kļūda: ' . $vaicajums->error));
}

$vaicajums->close();

$savienojums->close();
