<?php
require '../../admin/database/con_db.php';
session_start();

if (isset($_SESSION['lietotajaIdDt'])) {
    $lietotajaId = $_SESSION['lietotajaIdDt'];

    $stmt = $savienojums->prepare(
        'SELECT 
                                        mp.pieteikums_id,
                                        mp.izveidosanas_datums,
                                        mp.statuss,
                                        mr.majokla_tips,
                                        ad.pilseta,
                                        ad.iela,
                                        ad.majas_numurs,
                                        mr.cena
                                    FROM majuvieta_pieteikumi mp
                                    JOIN majuvieta_pirkt mr ON mp.id_majuvieta_pirkt = mr.pirkt_id
                                    JOIN majuvieta_adrese ad ON mr.id_adrese = ad.adrese_id
                                    WHERE mp.id_lietotajs = ?'
    );
    $stmt->bind_param('i', $lietotajaId);

    if ($stmt->execute()) {
        $rezultats = $stmt->get_result();
        $json = array();

        while ($ieraksts = $rezultats->fetch_assoc()) {
            $json[] = array(
                'id' => htmlspecialchars($ieraksts['pieteikums_id']),
                'izveidosanas_datums' => date("d.m.Y", strtotime($ieraksts['izveidosanas_datums'])),
                'statuss' => htmlspecialchars($ieraksts['statuss']),
                'majokla_tips' => htmlspecialchars($ieraksts['majokla_tips']),
                'pilseta' => htmlspecialchars($ieraksts['pilseta']),
                'iela' => htmlspecialchars($ieraksts['iela']),
                'majas_numurs' => htmlspecialchars($ieraksts['majas_numurs']),
                'cena' => htmlspecialchars($ieraksts['cena']),
            );
        }

        $jsonstring = json_encode($json);
        echo $jsonstring;
    } else {
        echo json_encode(array('error' => 'Query failed: ' . $stmt->error));
    }

    $stmt->close();
}

$savienojums->close();
