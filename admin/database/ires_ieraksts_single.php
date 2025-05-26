<?php
require 'con_db.php';
if (isset($_POST['id'])) {
    $id = htmlspecialchars($_POST['id']);
    $veids = "Iret";

    $vaicajums = $savienojums->prepare(
        "SELECT 
                mi.iziresana_id AS id,
                mi.registresanas_datums,
                mi.izrakstisanas_datums,
                mi.cena,
                mi.ip_adrese,
                mi.izveidosanas_datums,
                mi.atjauninasanas_datums,
                mr.majokla_tips,
                CONCAT(ad.pilseta, ' ', ad.iela, ' ', ad.majas_numurs) AS adrese,
                ml.epasts
            FROM majuvieta_iziresana mi
            JOIN majuvieta_iret mr ON mi.id_majuvieta_iret = mr.iret_id
            INNER JOIN majuvieta_adrese ad ON mr.iret_id = ad.id_sludinajums
            JOIN majuvieta_lietotaji ml ON mi.id_lietotajs = ml.lietotaja_id 
            WHERE ad.sludinajuma_veids = ? AND mi.iziresana_id = ?"
    );
    $vaicajums->bind_param('si', $veids, $id);
    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();
    if (!$rezultats) {
        // die('Kļūda: ' . $savienojums->error);
    }

    while ($ieraksts = $rezultats->fetch_assoc()) {
        $json[] = array(
            'id' => htmlspecialchars($ieraksts['id']),
            'epasts' => htmlspecialchars($ieraksts['epasts']),
            'izveidosanas_datums' => date("d.m.Y", strtotime($ieraksts['izveidosanas_datums'])),
            'atjauninasanas_datums' => date("d.m.Y", strtotime($ieraksts['atjauninasanas_datums'])),
            'iznomatsNo' => date("Y-m-d", strtotime($ieraksts['registresanas_datums'])),
            'iznomatsLidz' => date("Y-m-d", strtotime($ieraksts['izrakstisanas_datums'])),
            'cena' => htmlspecialchars($ieraksts['cena']),
            'majokla_tips' => htmlspecialchars($ieraksts['majokla_tips']),
            'adrese' => htmlspecialchars($ieraksts['adrese']),
            'ip_adrese' => htmlspecialchars($ieraksts['ip_adrese']),
        );
    }

    $vaicajums->close();
    $savienojums->close();

    $jsonstring = json_encode($json[0]);
    echo $jsonstring;
}
