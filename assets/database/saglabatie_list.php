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
// Maja - Pirkt
$query = "
    SELECT mv.pirkt_id AS id, mv.cena, mv.istabas, mv.platiba, mv.stavs_vai_stavi AS stavi,
           ad.pilseta, ad.iela, ad.majas_numurs,
           att.pirma_attela
    FROM dzivote_saglabatie ds
    JOIN majuvieta_pirkt mv ON ds.id_sludinajums = mv.pirkt_id
    INNER JOIN majuvieta_adrese ad ON mv.pirkt_id = ad.id_sludinajums
    INNER JOIN majuvieta_atteli att ON mv.pirkt_id = att.id_sludinajums
    WHERE ds.id_lietotajs = ?
    AND ds.sludinajuma_veids = 'Pirkt'
    AND ds.majokla_tips = 'Maja'
    AND att.sludinajuma_veids = 'Pirkt'
    AND ad.sludinajuma_veids = 'Pirkt'
    AND mv.statuss = 'Apsiprināts | Publicēts'
";

$stmt = $savienojums->prepare($query);
$stmt->bind_param("i", $lietotaja_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $json[] = [
        'id' => htmlspecialchars($row['id']),
        'veids' => 'Pirkt',
        'majokla_tips' => 'Maja',
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
$stmt->close();

// Dzivoklis - Pirkt
$query = "
    SELECT mv.pirkt_id AS id, mv.cena, mv.istabas, mv.platiba, mv.stavs_vai_stavi AS stavi,
           ad.pilseta, ad.iela, ad.majas_numurs,
           att.pirma_attela
    FROM dzivote_saglabatie ds
    JOIN majuvieta_pirkt mv ON ds.id_sludinajums = mv.pirkt_id
    INNER JOIN majuvieta_adrese ad ON mv.pirkt_id = ad.id_sludinajums
    INNER JOIN majuvieta_atteli att ON mv.pirkt_id = att.id_sludinajums
    WHERE ds.id_lietotajs = ?
    AND ds.sludinajuma_veids = 'Pirkt'
    AND ds.majokla_tips = 'Dzivoklis'
    AND att.sludinajuma_veids = 'Pirkt'
    AND ad.sludinajuma_veids = 'Pirkt'
    AND mv.statuss = 'Apsiprināts | Publicēts'
";

$stmt = $savienojums->prepare($query);
$stmt->bind_param("i", $lietotaja_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $json[] = [
        'id' => htmlspecialchars($row['id']),
        'veids' => 'Pirkt',
        'majokla_tips' => 'Dzivoklis',
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
$stmt->close();

// Māja - Iret
$query = "
    SELECT mv.iret_id AS id, mv.cena_diena, mv.cena_nedela, mv.cena_menesis,
           mv.istabas, mv.platiba, mv.stavi_vai_stavs AS stavi,
           ad.pilseta, ad.iela, ad.majas_numurs,
           att.pirma_attela
    FROM dzivote_saglabatie ds
    JOIN majuvieta_iret mv ON ds.id_sludinajums = mv.iret_id
    INNER JOIN majuvieta_adrese ad ON mv.iret_id = ad.id_sludinajums
    INNER JOIN majuvieta_atteli att ON mv.iret_id = att.id_sludinajums
    WHERE ds.id_lietotajs = ?
    AND ds.sludinajuma_veids = 'Iret'
    AND ds.majokla_tips = 'Maja'
    AND att.sludinajuma_veids = 'Iret'
    AND ad.sludinajuma_veids = 'Iret'
    AND mv.statuss = 'Apsiprināts | Publicēts'
";

$stmt = $savienojums->prepare($query);
$stmt->bind_param("i", $lietotaja_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $json[] = [
        'id' => htmlspecialchars($row['id']),
        'veids' => 'Iret',
        'majokla_tips' => 'Maja',
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
$stmt->close();

// Dzīvoklis - Iret
$query = "
    SELECT mv.iret_id AS id, mv.cena_diena, mv.cena_nedela, mv.cena_menesis,
           mv.istabas, mv.platiba, mv.stavi_vai_stavs AS stavi,
           ad.pilseta, ad.iela, ad.majas_numurs,
           att.pirma_attela
    FROM dzivote_saglabatie ds
    JOIN majuvieta_iret mv ON ds.id_sludinajums = mv.iret_id
    INNER JOIN majuvieta_adrese ad ON mv.iret_id = ad.id_sludinajums
    INNER JOIN majuvieta_atteli att ON mv.iret_id = att.id_sludinajums
    WHERE ds.id_lietotajs = ?
    AND ds.sludinajuma_veids = 'Iret'
    AND ds.majokla_tips = 'Dzivoklis'
    AND att.sludinajuma_veids = 'Iret'
    AND ad.sludinajuma_veids = 'Iret'
    AND mv.statuss = 'Apsiprināts | Publicēts'
";

$stmt = $savienojums->prepare($query);
$stmt->bind_param("i", $lietotaja_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $json[] = [
        'id' => htmlspecialchars($row['id']),
        'veids' => 'Iret',
        'majokla_tips' => 'Dzivoklis',
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
$stmt->close();

$savienojums->close();

echo json_encode($json);
