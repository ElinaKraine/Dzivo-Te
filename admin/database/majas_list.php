<?php
require 'con_db.php';

$prasibas = ["mp.majokla_tips = 'Mājas'"];
$params = [];

if (!empty($_GET['meklet'])) {
    $meklet = mysqli_real_escape_string($savienojums, htmlspecialchars($_GET['meklet']));
    $prasibas[] = "md.pilseta LIKE '%$meklet%'";
}

if (!empty($_GET['minCena'])) {
    $minCena = (int)$_GET['minCena'];
    $prasibas[] = "mp.cena >= $minCena";
}

if (!empty($_GET['maxCena'])) {
    $maxCena = (int)$_GET['maxCena'];
    $prasibas[] = "mp.cena <= $maxCena";
}

if (!empty($_GET['minIstabas'])) {
    $minIstabas = (int)$_GET['minIstabas'];
    $prasibas[] = "mp.istabas >= $minIstabas";
}

if (!empty($_GET['maxIstabas'])) {
    $maxIstabas = (int)$_GET['maxIstabas'];
    $prasibas[] = "mp.istabas <= $maxIstabas";
}

if (!empty($_GET['minPlatiba'])) {
    $minPlatiba = (int)$_GET['minPlatiba'];
    $prasibas[] = "mp.platiba >= $minPlatiba";
}

if (!empty($_GET['maxPlatiba'])) {
    $maxPlatiba = (int)$_GET['maxPlatiba'];
    $prasibas[] = "mp.platiba <= $maxPlatiba";
}

if (!empty($_GET['minStavi'])) {
    $minStavi = (int)$_GET['minStavi'];
    $prasibas[] = "mp.stavs_vai_stavi >= $minStavi";
}

if (!empty($_GET['maxStavi'])) {
    $maxStavi = (int)$_GET['maxStavi'];
    $prasibas[] = "mp.stavs_vai_stavi <= $maxStavi";
}

$prasibasRezultats = implode(" AND ", $prasibas);

$sortOptions = [
    "cena_asc" => "mp.cena ASC",
    "cena_desc" => "mp.cena DESC",
    "datums_asc" => "mp.izveidosanas_datums ASC",
    "datums_desc" => "mp.izveidosanas_datums DESC",
    "platiba_asc" => "mp.platiba ASC",
    "platiba_desc" => "mp.platiba DESC"
];

$sortBy = "mp.izveidosanas_datums DESC";

if (!empty($_GET['sort']) && array_key_exists($_GET['sort'], $sortOptions)) {
    $sortBy = $sortOptions[$_GET['sort']];
}

$vaicajums = "SELECT 
                mp.pirkt_id AS pirkt_id, 
                mp.cena AS cena, 
                mp.platiba AS platiba, 
                mp.istabas AS istabas, 
                mp.stavs_vai_stavi AS stavi, 
                md.pilseta AS pilseta, 
                md.iela AS iela, 
                md.majas_numurs AS majas_numurs, 
                ma.pirma_attela AS pirma_attela 
             FROM 
                majuvieta_pirkt mp 
             INNER JOIN 
                majuvieta_atteli ma ON mp.id_atteli = ma.attelu_kopums_id 
             INNER JOIN 
                majuvieta_adrese md ON mp.id_adrese = md.adrese_id 
             WHERE $prasibasRezultats 
             ORDER BY $sortBy";

$rezultats = mysqli_query($savienojums, $vaicajums);

$json = [];
while ($ieraksts = $rezultats->fetch_assoc()) {
    $json[] = array(
        'id' => htmlspecialchars($ieraksts['pirkt_id']),
        'cena' => htmlspecialchars($ieraksts['cena']),
        'platiba' => htmlspecialchars($ieraksts['platiba']),
        'istabas' => htmlspecialchars($ieraksts['istabas']),
        'stavi' => htmlspecialchars($ieraksts['stavi']),
        'pilseta' => htmlspecialchars($ieraksts['pilseta']),
        'iela' => htmlspecialchars($ieraksts['iela']),
        'majas_numurs' => htmlspecialchars($ieraksts['majas_numurs']),
        'pirma_attela' => base64_encode($ieraksts['pirma_attela']),
    );
}

echo json_encode($json);
