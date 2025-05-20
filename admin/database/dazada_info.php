<?php
require 'con_db.php';

// 1. Kopā sludinājumi (pirkt + īrēt)
$vaicajums1 = "SELECT 
            (SELECT COUNT(*) FROM majuvieta_pirkt) + 
            (SELECT COUNT(*) FROM majuvieta_iret) AS sludinajumu_skaits";
$sludinajumuSkaits = $savienojums->query($vaicajums1)->fetch_row()[0];

// 2. Rezervācijas pēdējo 24h laikā
$vaicajums2 = "SELECT COUNT(*) FROM majuvieta_iziresana WHERE izveidosanas_datums >= NOW() - INTERVAL 1 DAY";
$rezervacijuSkaits = $savienojums->query($vaicajums2)->fetch_row()[0];

// 3. Pabeigtie pieteikumi ar statusu "Mājoklis ir iegādāts" pēdējo 24h laikā
$vaicajums3 = "SELECT COUNT(*) FROM majuvieta_pieteikumi WHERE statuss = 'Mājoklis ir iegādāts' AND pedejais_izmainas_datums >= NOW() - INTERVAL 1 DAY";
$pardotoSkaits = $savienojums->query($vaicajums3)->fetch_row()[0];

// 4. Peļņa: īre + pārdošana pēdējo 24h laikā
// Īres summa
$vaicajums4 = "SELECT COALESCE(SUM(cena), 0) FROM majuvieta_iziresana WHERE izveidosanas_datums >= NOW() - INTERVAL 1 DAY";
$pelnaIresana = $savienojums->query($vaicajums4)->fetch_row()[0];

// Pārdošanas summa
$vaicajums5 = "
    SELECT COALESCE(SUM(mp.cena), 0)
    FROM majuvieta_pieteikumi mpie
    JOIN majuvieta_pirkt mp ON mpie.id_majuvieta_pirkt = mp.pirkt_id
    WHERE mpie.statuss = 'Mājoklis ir iegādāts'
      AND mpie.pedejais_izmainas_datums >= NOW() - INTERVAL 1 DAY
";
$pelnaPardosana = $savienojums->query($vaicajums5)->fetch_row()[0];

$totalPelna = $pelnaIresana + $pelnaPardosana;

echo json_encode([
    'sludinajumuSkaits' => $sludinajumuSkaits,
    'rezervacijuSkaits' => $rezervacijuSkaits,
    'pardotoSkaits' => $pardotoSkaits,
    'pelna' => $totalPelna
]);

$savienojums->close();
