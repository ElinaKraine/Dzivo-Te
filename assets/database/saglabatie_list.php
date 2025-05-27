<?php
session_start();
require '../../admin/database/con_db.php';
header('Content-Type: application/json');

if (isset($_SESSION['lietotajaIdDt'])) {
    $lietotaja_id = $_SESSION['lietotajaIdDt'];
    $json = [];

    //region Maja - Pirkt
    $sql_teikums = "SELECT mv.pirkt_id AS id, mv.cena, mv.istabas, mv.platiba, mv.stavs_vai_stavi AS stavi,
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

    $vaicajums = $savienojums->prepare($sql_teikums);
    $vaicajums->bind_param("i", $lietotaja_id);
    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();
    while ($ieraksts = $rezultats->fetch_assoc()) {
        $json[] = [
            'id' => htmlspecialchars($ieraksts['id']),
            'veids' => 'Pirkt',
            'majokla_tips' => 'Maja',
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
    $vaicajums->close();
    //endregion

    //region Dzivoklis - Pirkt
    $sql_teikums = "SELECT mv.pirkt_id AS id, mv.cena, mv.istabas, mv.platiba, mv.stavs_vai_stavi AS stavi,
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

    $vaicajums = $savienojums->prepare($sql_teikums);
    $vaicajums->bind_param("i", $lietotaja_id);
    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();
    while ($ieraksts = $rezultats->fetch_assoc()) {
        $json[] = [
            'id' => htmlspecialchars($ieraksts['id']),
            'veids' => 'Pirkt',
            'majokla_tips' => 'Dzivoklis',
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
    $vaicajums->close();
    //endregion

    //region Māja - Iret
    $sql_teikums = "SELECT mv.iret_id AS id, mv.cena_diena, mv.cena_nedela, mv.cena_menesis,
                        mv.istabas, mv.platiba, mv.stavs_vai_stavi AS stavi,
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

    $vaicajums = $savienojums->prepare($sql_teikums);
    $vaicajums->bind_param("i", $lietotaja_id);
    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();
    while ($ieraksts = $rezultats->fetch_assoc()) {
        $json[] = [
            'id' => htmlspecialchars($ieraksts['id']),
            'veids' => 'Iret',
            'majokla_tips' => 'Maja',
            'cena_diena' => htmlspecialchars($ieraksts['cena_diena']),
            'cena_nedela' => htmlspecialchars($ieraksts['cena_nedela']),
            'cena_menesis' => htmlspecialchars($ieraksts['cena_menesis']),
            'istabas' => htmlspecialchars($ieraksts['istabas']),
            'platiba' => htmlspecialchars($ieraksts['platiba']),
            'stavi' => htmlspecialchars($ieraksts['stavi']),
            'pilseta' => htmlspecialchars($ieraksts['pilseta']),
            'iela' => htmlspecialchars($ieraksts['iela']),
            'majas_numurs' => htmlspecialchars($ieraksts['majas_numurs']),
            'pirma_attela' => base64_encode($ieraksts['pirma_attela']),
        ];
    }
    $vaicajums->close();
    //endregion

    //region Dzīvoklis - Iret
    $sql_teikums = "SELECT mv.iret_id AS id, mv.cena_diena, mv.cena_nedela, mv.cena_menesis,
                        mv.istabas, mv.platiba, mv.stavs_vai_stavi AS stavi,
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

    $vaicajums = $savienojums->prepare($sql_teikums);
    $vaicajums->bind_param("i", $lietotaja_id);
    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();
    while ($ieraksts = $rezultats->fetch_assoc()) {
        $json[] = [
            'id' => htmlspecialchars($ieraksts['id']),
            'veids' => 'Iret',
            'majokla_tips' => 'Dzivoklis',
            'cena_diena' => htmlspecialchars($ieraksts['cena_diena']),
            'cena_nedela' => htmlspecialchars($ieraksts['cena_nedela']),
            'cena_menesis' => htmlspecialchars($ieraksts['cena_menesis']),
            'istabas' => htmlspecialchars($ieraksts['istabas']),
            'platiba' => htmlspecialchars($ieraksts['platiba']),
            'stavi' => htmlspecialchars($ieraksts['stavi']),
            'pilseta' => htmlspecialchars($ieraksts['pilseta']),
            'iela' => htmlspecialchars($ieraksts['iela']),
            'majas_numurs' => htmlspecialchars($ieraksts['majas_numurs']),
            'pirma_attela' => base64_encode($ieraksts['pirma_attela']),
        ];
    }
    $vaicajums->close();
    //endregion
} else {
    echo json_encode([]);
    exit;
}

$savienojums->close();
echo json_encode($json);
