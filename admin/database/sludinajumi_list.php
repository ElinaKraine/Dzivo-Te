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

while ($row = $rezultats->fetch_assoc()) {
    $json[] = [
        'id' => htmlspecialchars($row['id']),
        'veids' => $row['veids'],
        'tabula' => $row['tabula'],
        'majokla_tips' => htmlspecialchars($row['majokla_tips']),
        'cena' => htmlspecialchars($row['cena']),
        'platiba' => htmlspecialchars($row['platiba']),
        'statuss' => htmlspecialchars($row['statuss']),
        'izveidosanas_datums' => date("d.m.Y", strtotime($row['izveidosanas_datums'])),
        'adrese' => htmlspecialchars(
            $row['majokla_tips'] === 'Dzīvoklis' && !empty($row['dzivokla_numurs'])
                ? $row['adrese'] . '-' . $row['dzivokla_numurs']
                : $row['adrese']
        ),
        'epasts' => htmlspecialchars($row['epasts']),
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

while ($row = $rezultats->fetch_assoc()) {
    $json[] = [
        'id' => htmlspecialchars($row['id']),
        'veids' => $row['veids'],
        'tabula' => $row['tabula'],
        'majokla_tips' => htmlspecialchars($row['majokla_tips']),
        'cena' => htmlspecialchars($row['cena']),
        'platiba' => htmlspecialchars($row['platiba']),
        'statuss' => htmlspecialchars($row['statuss']),
        'izveidosanas_datums' => date("d.m.Y", strtotime($row['izveidosanas_datums'])),
        'adrese' => htmlspecialchars(
            $row['majokla_tips'] === 'Dzīvoklis' && !empty($row['dzivokla_numurs'])
                ? $row['adrese'] . '-' . $row['dzivokla_numurs']
                : $row['adrese']
        ),
        'epasts' => htmlspecialchars($row['epasts']),
    ];
}
$vaicajums->close();

$savienojums->close();

echo json_encode($json);
