<?php
require '../../admin/database/con_db.php';

if (!isset($_GET['noDziv']) || !isset($_GET['lidzDziv'])) {
    echo json_encode([]);
    exit;
}

$no = mysqli_real_escape_string($savienojums, $_GET['noDziv']);
$lidz = mysqli_real_escape_string($savienojums, $_GET['lidzDziv']);

$prasibas = [
    "mi.majokla_tips = 'Dzīvoklis'
    AND md.sludinajuma_veids = 'Iret'
    AND ma.sludinajuma_veids = 'Iret'
    AND mi.statuss = 'Apsiprināts | Publicēts'"
];

if (!empty($_GET['meklet'])) {
    $meklet = mysqli_real_escape_string($savienojums, htmlspecialchars($_GET['meklet']));
    $prasibas[] = "md.pilseta LIKE '%$meklet%' OR md.iela LIKE '%$meklet%'";
}

if (!empty($_GET['minIstabas'])) {
    $minIstabas = (int)$_GET['minIstabas'];
    $prasibas[] = "mi.istabas >= $minIstabas";
}

if (!empty($_GET['maxIstabas'])) {
    $maxIstabas = (int)$_GET['maxIstabas'];
    $prasibas[] = "mi.istabas <= $maxIstabas";
}

if (!empty($_GET['minPlatiba'])) {
    $minPlatiba = (int)$_GET['minPlatiba'];
    $prasibas[] = "mi.platiba >= $minPlatiba";
}

if (!empty($_GET['maxPlatiba'])) {
    $maxPlatiba = (int)$_GET['maxPlatiba'];
    $prasibas[] = "mi.platiba <= $maxPlatiba";
}

if (!empty($_GET['minStavi'])) {
    $minStavi = (int)$_GET['minStavi'];
    $prasibas[] = "mi.stavs_vai_stavi >= $minStavi";
}

if (!empty($_GET['maxStavi'])) {
    $maxStavi = (int)$_GET['maxStavi'];
    $prasibas[] = "mi.stavs_vai_stavi <= $maxStavi";
}

$prasibasSQL = "";
if (!empty($prasibas)) {
    $prasibasSQL = " AND " . implode(" AND ", $prasibas);
}

$sortOptions = [
    "cena_asc" => "mi.cena_diena ASC",
    "cena_desc" => "mi.cena_diena DESC",
    "platiba_asc" => "mi.platiba ASC",
    "platiba_desc" => "mi.platiba DESC",
    "datums_asc" => "mi.izveidosanas_datums ASC",
    "datums_desc" => "mi.izveidosanas_datums DESC"
];

$sortBy = "mi.izveidosanas_datums DESC";
if (!empty($_GET['sort']) && array_key_exists($_GET['sort'], $sortOptions)) {
    $sortBy = $sortOptions[$_GET['sort']];
}

$vaicajums = "SELECT 
                mi.iret_id AS iret_id, 
                mi.cena_diena AS cena_diena,
                mi.cena_nedela AS cena_nedela, 
                mi.cena_menesis AS cena_menesis, 
                mi.platiba AS platiba, 
                mi.istabas AS istabas, 
                mi.stavs_vai_stavi AS stavi, 
                md.pilseta AS pilseta, 
                md.iela AS iela, 
                md.majas_numurs AS majas_numurs,
                md.dzivokla_numurs AS dzivokla_numurs,
                ma.pirma_attela AS pirma_attela 
             FROM majuvieta_iret mi
             INNER JOIN majuvieta_adrese md ON mi.iret_id = md.id_sludinajums
             INNER JOIN majuvieta_atteli ma ON mi.iret_id = ma.id_sludinajums 
             WHERE mi.iret_id NOT IN (
                SELECT id_majuvieta_iret
                FROM majuvieta_iziresana
                WHERE NOT (
                    izrakstisanas_datums < '$no' OR
                    registresanas_datums > '$lidz'
                )
            )
            $prasibasSQL
            ORDER BY $sortBy";

$rezultats = mysqli_query($savienojums, $vaicajums);

$json = [];
while ($ieraksts = mysqli_fetch_assoc($rezultats)) {
    $json[] = [
        'id' => htmlspecialchars($ieraksts['iret_id']),
        'cena_diena' => htmlspecialchars($ieraksts['cena_diena']),
        'cena_nedela' => htmlspecialchars($ieraksts['cena_nedela']),
        'cena_menesis' => htmlspecialchars($ieraksts['cena_menesis']),
        'platiba' => htmlspecialchars($ieraksts['platiba']),
        'istabas' => htmlspecialchars($ieraksts['istabas']),
        'stavi' => htmlspecialchars($ieraksts['stavi']),
        'pilseta' => htmlspecialchars($ieraksts['pilseta']),
        'iela' => htmlspecialchars($ieraksts['iela']),
        'majas_numurs' => htmlspecialchars($ieraksts['majas_numurs']),
        'dzivokla_numurs' => htmlspecialchars($ieraksts['dzivokla_numurs']),
        'pirma_attela' => base64_encode($ieraksts['pirma_attela']),
    ];
}

$savienojums->close();
echo json_encode($json);
