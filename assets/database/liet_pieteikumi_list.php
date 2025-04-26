<?php
require '../../admin/database/con_db.php';
session_start();

if (isset($_SESSION['lietotajaIdDt'])) {
    $lietotajaId = $_SESSION['lietotajaIdDt'];

    $stmt = $savienojums->prepare(
        "SELECT 
            mp.pieteikums_id AS id,
            mp.statuss,
            mp.izveidosanas_datums,
            mv.cena,
            mv.majokla_tips,
            ad.pilseta,
            ad.iela,
            ad.majas_numurs,
            ml.epasts
        FROM majuvieta_pieteikumi mp
        JOIN majuvieta_pirkt mv ON mp.id_majuvieta_pirkt = mv.pirkt_id
        INNER JOIN majuvieta_adrese ad ON mv.pirkt_id = ad.id_sludinajums
        JOIN majuvieta_lietotaji ml ON mp.id_lietotajs = ml.lietotaja_id 
        WHERE mv.id_ipasnieks = ? AND ad.sludinajuma_veids = 'Pirkt'
        ORDER BY mp.izveidosanas_datums DESC"
    );
    $stmt->bind_param('i', $lietotajaId);

    if ($stmt->execute()) {
        $rezultats = $stmt->get_result();
        $json = array();

        while ($ieraksts = $rezultats->fetch_assoc()) {
            $json[] = [
                'id' => htmlspecialchars($ieraksts['id']),
                'epasts' => htmlspecialchars($ieraksts['epasts']),
                'statuss' => htmlspecialchars($ieraksts['statuss']),
                'izveidosanas_datums' => date("d.m.Y", strtotime($ieraksts['izveidosanas_datums'])),
                'cena' => htmlspecialchars($ieraksts['cena']),
                'majokla_tips' => htmlspecialchars($ieraksts['majokla_tips']),
                'pilseta' => htmlspecialchars($ieraksts['pilseta']),
                'iela' => htmlspecialchars($ieraksts['iela']),
                'majas_numurs' => htmlspecialchars($ieraksts['majas_numurs']),
            ];
        }

        $jsonstring = json_encode($json);
        echo $jsonstring;
    } else {
        echo json_encode(array('error' => 'Query failed: ' . $stmt->error));
    }

    $stmt->close();
}

$savienojums->close();
