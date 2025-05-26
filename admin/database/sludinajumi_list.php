<?php
require 'con_db.php';

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
                ad.dzivokla_numurs,
                CONCAT(ad.pilseta, ', ', ad.iela, ' ', ad.majas_numurs) AS adrese,
                ml.epasts AS epasts
            FROM majuvieta_pirkt mv
            INNER JOIN majuvieta_adrese ad ON mv.pirkt_id = ad.id_sludinajums
            INNER JOIN majuvieta_lietotaji ml ON mv.id_ipasnieks = ml.lietotaja_id
            WHERE ad.sludinajuma_veids = 'Pirkt'
            ORDER BY mv.izveidosanas_datums DESC";
$vaicajums = $savienojums->prepare($sql_teikums);
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
        'adrese' => htmlspecialchars(
            $ieraksts['majokla_tips'] === 'Dzīvoklis' && !empty($ieraksts['dzivokla_numurs'])
                ? $ieraksts['adrese'] . '-' . $ieraksts['dzivokla_numurs']
                : $ieraksts['adrese']
        ),
        'epasts' => htmlspecialchars($ieraksts['epasts']),
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
                ad.dzivokla_numurs,
                CONCAT(ad.pilseta, ', ', ad.iela, ' ', ad.majas_numurs) AS adrese,
                ml.epasts AS epasts
            FROM majuvieta_iret mv
            INNER JOIN majuvieta_adrese ad ON mv.iret_id = ad.id_sludinajums
            INNER JOIN majuvieta_lietotaji ml ON mv.id_ipasnieks = ml.lietotaja_id
            WHERE ad.sludinajuma_veids = 'Iret'
            ORDER BY mv.izveidosanas_datums DESC";
$vaicajums = $savienojums->prepare($sql_teikums);
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
        'adrese' => htmlspecialchars(
            $ieraksts['majokla_tips'] === 'Dzīvoklis' && !empty($ieraksts['dzivokla_numurs'])
                ? $ieraksts['adrese'] . '-' . $ieraksts['dzivokla_numurs']
                : $ieraksts['adrese']
        ),
        'epasts' => htmlspecialchars($ieraksts['epasts']),
    ];
}
$vaicajums->close();

$savienojums->close();

echo json_encode($json);
