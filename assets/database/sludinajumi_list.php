<?php
require '../../admin/database/con_db.php';
session_start();

if (isset($_SESSION['lietotajaIdDt'])) {
    $lietotajaId = $_SESSION['lietotajaIdDt'];
    $json = [];

    // Pirkt sludinājumi
    $sql_teikums = "SELECT 
                    'Pirkt' AS veids, 
                    'majuvieta_pirkt' AS tabula, 
                    mv.pirkt_id AS id, 
                    mv.majokla_tips, 
                    mv.cena, 
                    mv.platiba,
                    mv.statuss, 
                    mv.izveidosanas_datums,
                    ad.pilseta, 
                    ad.iela, 
                    ad.majas_numurs
                FROM majuvieta_pirkt mv
                INNER JOIN majuvieta_adrese ad ON mv.pirkt_id = ad.id_sludinajums
                WHERE mv.id_ipasnieks = ? AND ad.sludinajuma_veids = 'Pirkt' 
                AND mv.statuss != 'Dzēsts'
                ORDER BY mv.izveidosanas_datums DESC";
    $vaicajums = $savienojums->prepare($sql_teikums);
    $vaicajums->bind_param('i', $lietotajaId);
    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();

    while ($ieraksts = $rezultats->fetch_assoc()) {
        $json[] = [
            'id' => htmlspecialchars($ieraksts['id']),
            'veids' => $ieraksts['veids'],
            'tabula' => $ieraksts['tabula'],
            'majokla_tips' => htmlspecialchars($ieraksts['majokla_tips']),
            'cena' => htmlspecialchars($ieraksts['cena']),
            'platiba' => htmlspecialchars($ieraksts['platiba']),
            'statuss' => htmlspecialchars($rierakstsow['statuss']),
            'izveidosanas_datums' => date("d.m.Y", strtotime($ieraksts['izveidosanas_datums'])),
            'pilseta' => htmlspecialchars($ieraksts['pilseta']),
            'iela' => htmlspecialchars($ieraksts['iela']),
            'majas_numurs' => htmlspecialchars($ieraksts['majas_numurs']),
        ];
    }
    $vaicajums->close();

    // Iret sludinājumi
    $sql_teikums = "SELECT 
                    'Iret' AS veids, 
                    'majuvieta_iret' AS tabula, 
                    mv.iret_id AS id, 
                    mv.majokla_tips, 
                    mv.cena_menesis AS cena, 
                    mv.platiba,
                    mv.statuss, 
                    mv.izveidosanas_datums,
                    ad.pilseta, 
                    ad.iela, 
                    ad.majas_numurs
                FROM majuvieta_iret mv
                INNER JOIN majuvieta_adrese ad ON mv.iret_id = ad.id_sludinajums
                WHERE mv.id_ipasnieks = ? AND ad.sludinajuma_veids = 'Iret'
                AND mv.statuss != 'Dzēsts'
                ORDER BY mv.izveidosanas_datums DESC";
    $vaicajums = $savienojums->prepare($sql_teikums);
    $vaicajums->bind_param('i', $lietotajaId);
    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();

    while ($ieraksts = $rezultats->fetch_assoc()) {
        $json[] = [
            'id' => htmlspecialchars($ieraksts['id']),
            'veids' => $ieraksts['veids'],
            'tabula' => $ieraksts['tabula'],
            'majokla_tips' => htmlspecialchars($ieraksts['majokla_tips']),
            'cena' => htmlspecialchars($ieraksts['cena']),
            'platiba' => htmlspecialchars($ieraksts['platiba']),
            'statuss' => htmlspecialchars($ieraksts['statuss']),
            'izveidosanas_datums' => date("d.m.Y", strtotime($ieraksts['izveidosanas_datums'])),
            'pilseta' => htmlspecialchars($ieraksts['pilseta']),
            'iela' => htmlspecialchars($ieraksts['iela']),
            'majas_numurs' => htmlspecialchars($ieraksts['majas_numurs']),
        ];
    }
    $vaicajums->close();
}

$savienojums->close();

echo json_encode($json);
