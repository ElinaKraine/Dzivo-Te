<?php
session_start();
require '../../admin/database/con_db.php';

$majokla_tips = htmlspecialchars($_POST['majoklaTips']);
$majokla_veids = htmlspecialchars($_POST['majoklaVeids']);
$pilseta = htmlspecialchars($_POST['pilseta']);
$iela = htmlspecialchars($_POST['iela']);
$majas_numurs = htmlspecialchars($_POST['majasNumurs']);
$dzivokla_numurs = htmlspecialchars($_POST['dzivoklaNumurs']);
$cenaPirkt = htmlspecialchars($_POST['cenaPirkt']);
$cenaDiena = htmlspecialchars($_POST['cenaDiena']);
$cenaNedela = htmlspecialchars($_POST['cenaNedela']);
$cenaMenesi = htmlspecialchars($_POST['cenaMenesi']);
$platiba = htmlspecialchars($_POST['platiba']);
$zemes_platiba = htmlspecialchars($_POST['zemesPlatiba']);
$istabas = htmlspecialchars($_POST['istabas']);
$stavi = htmlspecialchars($_POST['stavi']);
$stavs = htmlspecialchars($_POST['stavs']);
$apraksts = htmlspecialchars($_POST['apraksts']);
$ip_adrese = $_SERVER['REMOTE_ADDR'];
$lietotajaId = $_SESSION['lietotajaIdDt'];
$tips = "";

function saglabat_attelus($savienojums, $atteli, $sludinajuma_veids, $sludinajuma_id)
{
    $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_atteli (
        pirma_attela, otra_attela, tresa_attela, ceturta_attela, piekta_attela,
        sesta_attela, septita_attela, astota_attela, devita_attela, desmita_attela,
        sludinajuma_veids, id_sludinajums
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $encoded = array_fill(0, 10, null);
    foreach ($atteli['tmp_name'] as $i => $tmp) {
        if ($i >= 10) break;
        if (filesize($tmp) <= 200 * 1024) {
            $encoded[$i] = file_get_contents($tmp);
        }
    }

    $parametruTips = str_repeat("s", 10) . "si";
    $visiParametri = array_merge($encoded, [$sludinajuma_veids, $sludinajuma_id]);
    $vaicajums->bind_param($parametruTips, ...$visiParametri);
    if ($vaicajums->execute()) {
        // echo "Attēli veiksmīgi izveidoti";
    } else {
        // echo "Kļūda: " . $vaicajums->error;
    }
    $vaicajums->close();
}

function saglabat_adresi($savienojums, $sludinajuma_veids, $sludinajuma_id, $pilseta, $iela, $majas_numurs, $dzivokla_numurs = null)
{
    if ($dzivokla_numurs) {
        $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_adrese (pilseta, iela, majas_numurs, dzivokla_numurs, sludinajuma_veids, id_sludinajums) VALUES (?, ?, ?, ?, ?, ?)");
        $vaicajums->bind_param("sssssi", $pilseta, $iela, $majas_numurs, $dzivokla_numurs, $sludinajuma_veids, $sludinajuma_id);
    } else {
        $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_adrese (pilseta, iela, majas_numurs, sludinajuma_veids, id_sludinajums) VALUES (?, ?, ?, ?, ?)");
        $vaicajums->bind_param("ssssi", $pilseta, $iela, $majas_numurs, $sludinajuma_veids, $sludinajuma_id);
    }
    if ($vaicajums->execute()) {
        // echo "Adrese veiksmīgs izveidots";
    } else {
        // echo "Kļūda: " . $vaicajums->error;
    }
    $vaicajums->close();
}

function ir_vismaz_viens_attels($files)
{
    foreach ($files['tmp_name'] as $tmp) {
        if (!empty($tmp) && filesize($tmp) <= 200 * 1024) {
            return true;
        }
    }
    return false;
}

function vai_adrese_jau_eksiste($savienojums, $pilseta, $iela, $majas_numurs, $dzivokla_numurs)
{
    if ($dzivokla_numurs) {
        $vaicajums = $savienojums->prepare("
            SELECT * FROM majuvieta_adrese a
            LEFT JOIN majuvieta_pirkt p ON p.pirkt_id = a.id_sludinajums AND a.sludinajuma_veids = 'Pirkt'
            LEFT JOIN majuvieta_iret i ON i.iret_id = a.id_sludinajums AND a.sludinajuma_veids = 'Iret'
            WHERE a.pilseta = ?
            AND a.iela = ?
            AND a.majas_numurs = ?
            AND a.dzivokla_numurs = ?
            AND ((a.sludinajuma_veids = 'Pirkt' AND p.statuss != 'Dzēsts')
                OR
                (a.sludinajuma_veids = 'Iret' AND i.statuss != 'Dzēsts'));
        ");
        $vaicajums->bind_param("ssss", $pilseta, $iela, $majas_numurs, $dzivokla_numurs);
    } else {
        $vaicajums = $savienojums->prepare("
            SELECT * FROM majuvieta_adrese a
            LEFT JOIN majuvieta_pirkt p ON p.pirkt_id = a.id_sludinajums AND a.sludinajuma_veids = 'Pirkt'
            LEFT JOIN majuvieta_iret i ON i.iret_id = a.id_sludinajums AND a.sludinajuma_veids = 'Iret'
            WHERE a.pilseta = ?
            AND a.iela = ?
            AND a.majas_numurs = ?
            AND ((a.sludinajuma_veids = 'Pirkt' AND p.statuss != 'Dzēsts')
                OR
                (a.sludinajuma_veids = 'Iret' AND i.statuss != 'Dzēsts'));
        ");
        $vaicajums->bind_param("sss", $pilseta, $iela, $majas_numurs);
    }

    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();
    $eksiste = $rezultats->num_rows > 0;
    $vaicajums->close();

    return $eksiste;
}

if (!empty($majokla_tips) && !empty($majokla_veids) && !empty($pilseta) && !empty($iela) && !empty($majas_numurs) && !empty($platiba) && isset($_FILES['atteli']) && ir_vismaz_viens_attels($_FILES['atteli'])) {
    if (vai_adrese_jau_eksiste($savienojums, $pilseta, $iela, $majas_numurs, $dzivokla_numurs)) {
        echo "Šāda adrese jau eksistē citā sludinājumā!";
        exit;
    }
    if ($majokla_tips === 'maja') {
        $tips = "Mājas";
        if (!empty($zemes_platiba) && !empty($stavi)) {
            if ($majokla_veids === 'pirkt' && !empty($cenaPirkt) && $cenaPirkt >= 1) {
                //region Māja pārdošanai
                $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_pirkt (majokla_tips, id_ipasnieks, cena, platiba, zemes_platiba, istabas, stavs_vai_stavi, apraksts, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $vaicajums->bind_param("siiiiisss", $tips, $lietotajaId, $cenaPirkt, $platiba, $zemes_platiba, $istabas, $stavi, $apraksts, $ip_adrese);
                if ($vaicajums->execute()) {
                    // echo "Māja pārdošānai veiksmīgs izveidots";
                } else {
                    // echo "Kļūda: " . $savienojums->error;
                }
                $sludinajuma_id = $vaicajums->insert_id;
                $vaicajums->close();
                $sludinajuma_veids = "Pirkt";

                saglabat_adresi($savienojums, $sludinajuma_veids, $sludinajuma_id, $pilseta, $iela, $majas_numurs, $dzivokla_numurs);
                saglabat_attelus($savienojums, $_FILES['atteli'], $sludinajuma_veids, $sludinajuma_id);

                echo "Māja pārdošānai veiksmīgs izveidots! Lūdzu, gaidiet, kad administrācija apstiprinās šo sludinājumu.";
                //endregion
            } elseif ($majokla_veids === 'iret' && !empty($cenaDiena) && !empty($cenaNedela) && !empty($cenaMenesi) && $cenaDiena >= 1 && $cenaNedela >= 1 && $cenaMenesi >= 1) {
                //region Māja īrēšanai
                $tips = "Mājas";
                $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_iret (majokla_tips, id_ipasnieks, cena_diena, cena_nedela, cena_menesis, platiba, zemes_platiba, istabas, stavs_vai_stavi, apraksts, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $vaicajums->bind_param("siiiiiiisss", $tips, $lietotajaId, $cenaDiena, $cenaNedela, $cenaMenesi, $platiba, $zemes_platiba, $istabas, $stavi, $apraksts, $ip_adrese);
                if ($vaicajums->execute()) {
                    // echo "Māja īrēšanai veiksmīgs izveidots";
                } else {
                    // echo "Kļūda: " . $savienojums->error;
                }
                $sludinajuma_id = $vaicajums->insert_id;
                $vaicajums->close();
                $sludinajuma_veids = "Iret";

                saglabat_adresi($savienojums, $sludinajuma_veids, $sludinajuma_id, $pilseta, $iela, $majas_numurs, $dzivokla_numurs);
                saglabat_attelus($savienojums, $_FILES['atteli'], $sludinajuma_veids, $sludinajuma_id);

                echo "Māja īrēšanai veiksmīgs izveidots! Lūdzu, gaidiet, kad administrācija apstiprinās šo sludinājumu.";
                //endregion
            } else {
                echo "Visi ievadas lauki nav aizpildīti!";
            }
        } else {
            echo "Visi ievadas lauki nav aizpildīti!";
        }
    } elseif ($majokla_tips === 'dzivoklis') {
        $tips = "Dzīvoklis";
        if (!empty($dzivokla_numurs) && !empty($stavs)) {
            if ($majokla_veids === 'pirkt' && !empty($cenaPirkt) && $cenaPirkt >= 1) {
                //region Dzīvoklis pārdošanai
                $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_pirkt (majokla_tips, id_ipasnieks, cena, platiba, istabas, stavs_vai_stavi, apraksts, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $vaicajums->bind_param("siiiisss", $tips, $lietotajaId, $cenaPirkt, $platiba, $istabas, $stavs, $apraksts, $ip_adrese);
                if ($vaicajums->execute()) {
                    // echo "Dzīvoklis pārdošānai veiksmīgs izveidots";
                } else {
                    // echo "Kļūda: " . $savienojums->error;
                }
                $sludinajuma_id = $vaicajums->insert_id;
                $vaicajums->close();
                $sludinajuma_veids = "Pirkt";

                saglabat_adresi($savienojums, $sludinajuma_veids, $sludinajuma_id, $pilseta, $iela, $majas_numurs, $dzivokla_numurs);
                saglabat_attelus($savienojums, $_FILES['atteli'], $sludinajuma_veids, $sludinajuma_id);

                echo "Dzīvoklis pārdošānai veiksmīgs izveidots! Lūdzu, gaidiet, kad administrācija apstiprinās šo sludinājumu.";
                //endregion
            } elseif ($majokla_veids === 'iret' && !empty($cenaDiena) && !empty($cenaNedela) && !empty($cenaMenesi) && $cenaDiena >= 1 && $cenaNedela >= 1 && $cenaMenesi >= 1) {
                //region Dzīvoklis īrēšanai
                $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_iret (majokla_tips, id_ipasnieks, cena_diena, cena_nedela, cena_menesis, platiba, istabas, stavs_vai_stavi, apraksts, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $vaicajums->bind_param("siiiiiisss", $tips, $lietotajaId, $cenaDiena, $cenaNedela, $cenaMenesi, $platiba, $istabas, $stavs, $apraksts, $ip_adrese);
                if ($vaicajums->execute()) {
                    // echo "Dzīvoklis īrešanai veiksmīgs izveidots";
                } else {
                    // echo "Kļūda: " . $savienojums->error;
                }

                $sludinajuma_id = $vaicajums->insert_id;
                $vaicajums->close();
                $sludinajuma_veids = "Iret";

                saglabat_adresi($savienojums, $sludinajuma_veids, $sludinajuma_id, $pilseta, $iela, $majas_numurs, $dzivokla_numurs);
                saglabat_attelus($savienojums, $_FILES['atteli'], $sludinajuma_veids, $sludinajuma_id);

                echo "Dzīvoklis īrešanai veiksmīgs izveidots! Lūdzu, gaidiet, kad administrācija apstiprinās šo sludinājumu.";
                //endregion
            } else {
                echo "Visi ievadas lauki nav aizpildīti!";
            }
        } else {
            echo "Visi ievadas lauki nav aizpildīti!";
        }
    }
} else {
    echo "Visi ievadas lauki nav aizpildīti!";
}
$savienojums->close();
