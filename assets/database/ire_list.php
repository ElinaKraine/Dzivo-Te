<?php
require '../../admin/database/con_db.php';
session_start();

if (isset($_SESSION['lietotajaIdDt'])) {
    $lietotajaId = $_SESSION['lietotajaIdDt'];

    $vaicajums = $savienojums->prepare(
        "SELECT
            mi.iziresana_id AS id,
            mi.registresanas_datums,
            mi.izrakstisanas_datums,
            mi.cena,
            mi.izveidosanas_datums,
            mr.majokla_tips,
            ad.pilseta,
            ad.iela,
            ad.majas_numurs
        FROM majuvieta_iziresana mi
        JOIN majuvieta_iret mr ON mi.id_majuvieta_iret = mr.iret_id
        INNER JOIN majuvieta_adrese ad ON mr.iret_id = ad.id_sludinajums
        WHERE id_lietotajs = ? AND ad.sludinajuma_veids = 'Iret'
        ORDER BY mi.izveidosanas_datums DESC"
    );
    $vaicajums->bind_param('i', $lietotajaId);

    if ($vaicajums->execute()) {
        $rezultats = $vaicajums->get_result();
        $json = array();

        while ($ieraksts = $rezultats->fetch_assoc()) {
            $json[] = array(
                'id' => htmlspecialchars($ieraksts['id']),
                'registresanas_datums' => date("d.m.Y", strtotime($ieraksts['registresanas_datums'])),
                'izrakstisanas_datums' => date("d.m.Y", strtotime($ieraksts['izrakstisanas_datums'])),
                'cena' => htmlspecialchars($ieraksts['cena']),
                'izveidosanas_datums' => date("d.m.Y", strtotime($ieraksts['izveidosanas_datums'])),
                'majokla_tips' => htmlspecialchars($ieraksts['majokla_tips']),
                'pilseta' => htmlspecialchars($ieraksts['pilseta']),
                'iela' => htmlspecialchars($ieraksts['iela']),
                'majas_numurs' => htmlspecialchars($ieraksts['majas_numurs'])
            );
        }

        $jsonstring = json_encode($json);
        echo $jsonstring;
    } else {
        // echo json_encode(array('error' => 'Kļūda: ' . $vaicajums->error));
    }

    $vaicajums->close();
}

$savienojums->close();
