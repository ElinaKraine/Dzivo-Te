<?php
session_start();
require '../../admin/database/con_db.php';

if (!isset($_SESSION['lietotajaIdDt'])) {
    echo json_encode([]);
    exit;
}

$lietotaja_id = $_SESSION['lietotajaIdDt'];

$vaicajums = "
    SELECT mv.pirkt_id AS id, mv.cena AS cena, mv.istabas, mv.platiba, mv.stavs_vai_stavi AS stavi,
           ad.pilseta AS pilseta, ad.iela AS iela, ad.majas_numurs AS majas_numurs,
           att.pirma_attela AS pirma_attela
    FROM dzivote_saglabatie ds
    JOIN majuvieta_pirkt mv ON ds.id_sludinajums = mv.pirkt_id
    JOIN majuvieta_adrese ad ON mv.id_adrese = ad.adrese_id
    JOIN majuvieta_atteli att ON mv.id_atteli = att.attelu_kopums_id
    WHERE ds.id_lietotajs = $lietotaja_id
";

$rezultats = mysqli_query($savienojums, $vaicajums);

$json = [];
while ($ieraksts = mysqli_fetch_assoc($rezultats)) {
    $json[] = [
        'id' => htmlspecialchars($ieraksts['id']),
        'cena' => htmlspecialchars($ieraksts['cena']),
        'istabas' => htmlspecialchars($ieraksts['istabas']),
        'platiba' => htmlspecialchars($ieraksts['platiba']),
        'stavi' => htmlspecialchars($ieraksts['stavi']),
        'pilseta' => htmlspecialchars($ieraksts['pilseta']),
        'iela' => htmlspecialchars($ieraksts['iela']),
        'majas_numurs' => htmlspecialchars($ieraksts['majas_numurs']),
        'pirma_attela' => base64_encode($ieraksts['pirma_attela']),
    ];
}

echo json_encode($json);
