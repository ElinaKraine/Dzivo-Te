<?php
session_start();
require '../../admin/database/con_db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['lietotajaIdDt'])) {
    echo json_encode([]);
    exit;
}

$lietotaja_id = $_SESSION['lietotajaIdDt'];
$json = [];

$pirkt_vaicajums = "
    SELECT mv.pirkt_id AS id, mv.cena, mv.istabas, mv.platiba, mv.stavs_vai_stavi AS stavi,
           ad.pilseta, ad.iela, ad.majas_numurs,
           att.pirma_attela
    FROM dzivote_saglabatie ds
    JOIN majuvieta_pirkt mv ON ds.id_sludinajums = mv.pirkt_id
    JOIN majuvieta_adrese ad ON mv.id_adrese = ad.adrese_id
    JOIN majuvieta_atteli att ON mv.id_atteli = att.attelu_kopums_id
    WHERE ds.id_lietotajs = ? AND ds.sludinajuma_veids = 'Pirkt'
";

$stmt1 = $savienojums->prepare($pirkt_vaicajums);
$stmt1->bind_param("i", $lietotaja_id);
$stmt1->execute();
$rez1 = $stmt1->get_result();

while ($row = $rez1->fetch_assoc()) {
    $json[] = [
        'id' => htmlspecialchars($row['id']),
        'veids' => 'Pirkt',
        'cena' => htmlspecialchars($row['cena']),
        'istabas' => htmlspecialchars($row['istabas']),
        'platiba' => htmlspecialchars($row['platiba']),
        'stavi' => htmlspecialchars($row['stavi']),
        'pilseta' => htmlspecialchars($row['pilseta']),
        'iela' => htmlspecialchars($row['iela']),
        'majas_numurs' => htmlspecialchars($row['majas_numurs']),
        'pirma_attela' => base64_encode($row['pirma_attela']),
    ];
}
$stmt1->close();

$iret_vaicajums = "
    SELECT mv.iret_id AS id, mv.cena_diena, mv.cena_nedela, mv.cena_menesis,
           mv.istabas, mv.platiba, mv.stavi_vai_stavs AS stavi,
           ad.pilseta, ad.iela, ad.majas_numurs,
           att.pirma_attela
    FROM dzivote_saglabatie ds
    JOIN majuvieta_iret mv ON ds.id_sludinajums = mv.iret_id
    JOIN majuvieta_adrese ad ON mv.id_adrese = ad.adrese_id
    JOIN majuvieta_atteli att ON mv.id_atteli = att.attelu_kopums_id
    WHERE ds.id_lietotajs = ? AND ds.sludinajuma_veids = 'Iret'
";

$stmt2 = $savienojums->prepare($iret_vaicajums);
$stmt2->bind_param("i", $lietotaja_id);
$stmt2->execute();
$rez2 = $stmt2->get_result();

while ($row = $rez2->fetch_assoc()) {
    $json[] = [
        'id' => htmlspecialchars($row['id']),
        'veids' => 'Iret',
        'cena_diena' => htmlspecialchars($row['cena_diena']),
        'cena_nedela' => htmlspecialchars($row['cena_nedela']),
        'cena_menesis' => htmlspecialchars($row['cena_menesis']),
        'istabas' => htmlspecialchars($row['istabas']),
        'platiba' => htmlspecialchars($row['platiba']),
        'stavi' => htmlspecialchars($row['stavi']),
        'pilseta' => htmlspecialchars($row['pilseta']),
        'iela' => htmlspecialchars($row['iela']),
        'majas_numurs' => htmlspecialchars($row['majas_numurs']),
        'pirma_attela' => base64_encode($row['pirma_attela']),
    ];
}
$stmt2->close();
$savienojums->close();

echo json_encode($json);
