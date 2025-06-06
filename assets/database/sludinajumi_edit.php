<?php
require '../../admin/database/con_db.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

function atjauninat_attelus($savienojums, $atteli, $sludinajuma_veids, $sludinajuma_id)
{
    $vaicajums = $savienojums->prepare("UPDATE majuvieta_atteli SET pirma_attela = ?, otra_attela = ?, tresa_attela = ?, ceturta_attela = ?, piekta_attela = ?,
        sesta_attela = ?, septita_attela = ?, astota_attela = ?, devita_attela = ?, desmita_attela = ? WHERE sludinajuma_veids = ? AND id_sludinajums = ?");

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
        // echo "Attēli veiksmīgi atjaunināti";
    } else {
        // echo "Kļūda: " . $vaicajums->error;
    }
    $vaicajums->close();
}

function atjauninat_adresi($savienojums, $sludinajuma_veids, $sludinajuma_id, $pilseta, $iela, $majas_numurs, $dzivokla_numurs = null)
{
    if ($dzivokla_numurs) {
        $vaicajums = $savienojums->prepare("UPDATE majuvieta_adrese SET pilseta = ?, iela = ?, majas_numurs = ?, dzivokla_numurs = ? WHERE sludinajuma_veids = ? AND id_sludinajums = ?");
        $vaicajums->bind_param("sssssi", $pilseta, $iela, $majas_numurs, $dzivokla_numurs, $sludinajuma_veids, $sludinajuma_id);
    } else {
        $vaicajums = $savienojums->prepare("UPDATE majuvieta_adrese SET pilseta = ?, iela = ?, majas_numurs = ? WHERE sludinajuma_veids = ? AND id_sludinajums = ?");
        $vaicajums->bind_param("ssssi", $pilseta, $iela, $majas_numurs, $sludinajuma_veids, $sludinajuma_id);
    }
    if ($vaicajums->execute()) {
        // echo "Adrese veiksmīgs atjaunināts";
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

function nomainit_sludinajumu($savienojums, $vecaisVeids, $id, $veids, $sqlIevietosana, $paramIevietosana, $lietotajaId)
{
    // Dzēšana no vecas tabulas
    $vecaTabula = $vecaisVeids === "pirkt" ? "majuvieta_pirkt" : "majuvieta_iret";
    $idLauks = $vecaisVeids === "pirkt" ? "pirkt_id" : "iret_id";

    $vaicajums = $savienojums->prepare("DELETE FROM $vecaTabula WHERE $idLauks = ?");
    $vaicajums->bind_param("i", $id);
    $vaicajums->execute();
    $vaicajums->close();

    // Ievietošana jaunā tabulā
    $vaicajums = $savienojums->prepare($sqlIevietosana);
    $vaicajums->bind_param(...$paramIevietosana);
    $vaicajums->execute();
    $jaunsId = $vaicajums->insert_id;
    $vaicajums->close();

    // Veids un ID atjaunināšana tabulās majuvieta_adrese un majuvieta_atteli
    foreach (['majuvieta_adrese', 'majuvieta_atteli'] as $tabula) {
        $sql = "UPDATE $tabula SET sludinajuma_veids = ?, id_sludinajums = ? WHERE sludinajuma_veids = ? AND id_sludinajums = ?";
        $vaicajums = $savienojums->prepare($sql);
        $vaicajums->bind_param("sisi", $veids, $jaunsId, $vecaisVeids, $id);
        $vaicajums->execute();
        $vaicajums->close();
    }

    return $jaunsId;
}

function vai_adrese_jau_eksiste($savienojums, $pilseta, $iela, $majas_numurs, $dzivokla_numurs, $veids, $id)
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
                (a.sludinajuma_veids = 'Iret' AND i.statuss != 'Dzēsts'))
            AND NOT (a.sludinajuma_veids = ? AND a.id_sludinajums = ?);
        ");
        $vaicajums->bind_param("sssssi", $pilseta, $iela, $majas_numurs, $dzivokla_numurs, $veids, $id);
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
                (a.sludinajuma_veids = 'Iret' AND i.statuss != 'Dzēsts'))
            AND NOT (a.sludinajuma_veids = ? AND a.id_sludinajums = ?);
        ");
        $vaicajums->bind_param("ssssi", $pilseta, $iela, $majas_numurs, $veids, $id);
    }

    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();
    $eksiste = $rezultats->num_rows > 0;
    $vaicajums->close();

    return $eksiste;
}

function iegut_ipasnieka_id($savienojums, $veids, $id)
{
    $ipasniekaId = 0;
    if ($veids === 'pirkt') {
        $vaicajums = $savienojums->prepare("SELECT id_ipasnieks FROM majuvieta_pirkt WHERE pirkt_id = ?");
    } elseif ($veids === 'iret') {
        $vaicajums = $savienojums->prepare("SELECT id_ipasnieks FROM majuvieta_iret WHERE iret_id = ?");
    } else {
        return null;
    }

    $vaicajums->bind_param("i", $id);
    $vaicajums->execute();
    $vaicajums->bind_result($ipasniekaId);
    $vaicajums->fetch();
    $vaicajums->close();

    return $ipasniekaId;
}


$loma = "";
switch ($_SESSION['lietotajaLomaMV']) {
    case 'Administrators':
        $loma = "Admin";
        break;
    case 'Moderators':
        $loma = "Admin";
        break;
    case 'Lietotājs':
        $loma = "liet";
        break;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //region Iegūst datus
    if ($loma === "Admin") {
        $id = intval($_POST['slud_ID']);
        $veids = $_POST['majoklaVeidsAdmin'];
        $tips = $_POST['majoklaTipsAdmin'];
        $stavs_vai_stavi = $tips === "dzivoklis" ? ($_POST['stavsAdmin'] ?? null) : ($_POST['staviAdmin'] ?? null);
        $pilseta = $_POST['pilsetaAdmin'];
        $iela = $_POST['ielaAdmin'];
        $majasNumurs = $_POST['majasNumursAdmin'];
        $dzivoklaNumurs = $_POST['dzivoklaNumursAdmin'] ?? null;
        $platiba = $_POST['platibaAdmin'];
        $zemesPlatiba = $_POST['zemesPlatibaAdmin'] ?? null;
        $istabas = $_POST['istabasAdmin'];
        $apraksts = $_POST['aprakstsAdmin'];
        $cenaPirkt = $_POST['cenaPirktAdmin'] ?? null;
        $cenaDiena = $_POST['cenaDienaAdmin'] ?? null;
        $cenaNedela = $_POST['cenaNedelaAdmin'] ?? null;
        $cenaMenesi = $_POST['cenaMenesiAdmin'] ?? null;
        $statuss = $veids === 'pirkt' ?  $_POST['sludNomainitStatusuAdminPirkt'] : $_POST['sludNomainitStatusuAdminIret'];
        $atteli = $_FILES['atteliAdmin'];
    } elseif ($loma === "liet") {
        $id = intval($_POST['id_sludinajums']);
        $veids = $_POST['majoklaVeids'];
        $tips = $_POST['majoklaTips'];
        $stavs_vai_stavi = $tips === "dzivoklis" ? ($_POST['stavs'] ?? null) : ($_POST['stavi'] ?? null);
        $pilseta = $_POST['pilseta'];
        $iela = $_POST['iela'];
        $majasNumurs = $_POST['majasNumurs'];
        $dzivoklaNumurs = $_POST['dzivoklaNumurs'] ?? null;
        $platiba = $_POST['platiba'];
        $zemesPlatiba = $_POST['zemesPlatiba'] ?? null;
        $istabas = $_POST['istabas'];
        $apraksts = $_POST['apraksts'];
        $cenaPirkt = $_POST['cenaPirkt'] ?? null;
        $cenaDiena = $_POST['cenaDiena'] ?? null;
        $cenaNedela = $_POST['cenaNedela'] ?? null;
        $cenaMenesi = $_POST['cenaMenesi'] ?? null;
        $statuss = "Iesniegts sludinājums";
        $atteli = $_FILES['atteli'];
    }
    $nomainitAtteli = $_POST['nomainitAtteli'] ?? 'ne';
    $datums = date("Y-m-d H:i:s");
    $lietotajaId = $_SESSION['lietotajaIdDt'];
    $ip_adrese = $_SERVER['REMOTE_ADDR'];
    //endregion

    //region Esošā (vecā) tipa noteikšana
    $vecaisVeids = null;
    $vaicajums = $savienojums->prepare("SELECT 1 FROM majuvieta_pirkt WHERE pirkt_id = ?");
    $vaicajums->bind_param("i", $id);
    $vaicajums->execute();
    if ($vaicajums->get_result()->num_rows > 0) {
        $vecaisVeids = "pirkt";
    }
    $vaicajums->close();

    if (!$vecaisVeids) {
        $vaicajums = $savienojums->prepare("SELECT 1 FROM majuvieta_iret WHERE iret_id = ?");
        $vaicajums->bind_param("i", $id);
        $vaicajums->execute();
        if ($vaicajums->get_result()->num_rows > 0) {
            $vecaisVeids = "iret";
        }
        $vaicajums->close();
    }

    if (!$vecaisVeids) {
        echo "Nevar atrast sludinājumu datubāzē.";
        exit;
    }
    //endregion

    //region Rediģēšanas aizliegums, ja ir pieteikumi/rezervācijas vai ja adrese jau pastāv
    if ($vecaisVeids === "pirkt") {
        $vaicajums = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_pieteikumi WHERE id_majuvieta_pirkt = ? AND statuss != 'Atteikums'");
        $vaicajums->bind_param("i", $id);
        $vaicajums->execute();
        $vaicajums->bind_result($count);
        $vaicajums->fetch();
        $vaicajums->close();
        if ($count > 0) {
            echo "Šo sludinājumu nevar rediģēt, jo tam jau ir pieteikumi.";
            exit;
        }

        if ($loma === "liet") {
            $vaicajums = $savienojums->prepare("SELECT statuss FROM majuvieta_pirkt WHERE pirkt_id = ?");
            $vaicajums->bind_param("i", $id);
            $vaicajums->execute();
            $vaicajums->bind_result($sludinajumsStatuss);
            $vaicajums->fetch();
            $vaicajums->close();
            if ($sludinajumsStatuss == "Mājoklis ir iegādāts") {
                echo "Šo sludinājumu nevar rediģēt, jo mājoklis jau ir iegādāts.";
                exit;
            }
        }
    } else {
        $sodien = date("Y-m-d");
        $vaicajums = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_iziresana WHERE id_majuvieta_iret = ? AND izrakstisanas_datums >= ?");
        $vaicajums->bind_param("is", $id, $sodien);
        $vaicajums->execute();
        $vaicajums->bind_result($count);
        $vaicajums->fetch();
        $vaicajums->close();
        if ($count > 0) {
            echo "Šo sludinājumu nevar rediģēt, jo tam jau ir rezervācijas.";
            exit;
        }
    }
    //endregion

    $vai_veids_ir_nomainits = $vecaisVeids !== $veids;

    $veidsKapitals = $veids === 'pirkt' ? 'Pirkt' : 'Iret';
    if (!$vai_veids_ir_nomainits) {
        if (vai_adrese_jau_eksiste($savienojums, $pilseta, $iela, $majasNumurs, $dzivoklaNumurs, $veidsKapitals, $id)) {
            echo "Šāda adrese jau eksistē aktīvā sludinājumā!";
            exit;
        }
    }

    if (!empty($veids) && !empty($pilseta) && !empty($iela) && !empty($majasNumurs) && !empty($platiba) && !empty($stavs_vai_stavi)) {
        if ($nomainitAtteli === "ja" && !isset($atteli)) {
            echo "Visi ievadas lauki nav aizpildīti!";
            exit;
        } elseif ($nomainitAtteli === "ja" && !ir_vismaz_viens_attels($atteli)) {
            echo "Visi ievadas lauki nav aizpildīti!";
            exit;
        }
        if ($tips === 'maja' && !empty($zemesPlatiba)) {
            if ($veids === 'pirkt' && !empty($cenaPirkt) && $cenaPirkt >= 1) {
                //region Māja pārdošanai
                if ($vai_veids_ir_nomainits) {
                    $sqlTeikums = "INSERT INTO majuvieta_pirkt (id_ipasnieks, cena, platiba, zemes_platiba, istabas, stavs_vai_stavi, apraksts, atjauninasanas_datums, statuss, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $ipasniekaId = iegut_ipasnieka_id($savienojums, $vecaisVeids, $id);
                    if ($ipasniekaId === 0) {
                        echo "Neizdevās noteikt īpašnieku!";
                        exit;
                    }

                    $params = ["iiiiisssss", $ipasniekaId, $cenaPirkt, $platiba, $zemesPlatiba, $istabas, $stavs_vai_stavi, $apraksts, $datums, $statuss, $ip_adrese];

                    $jaunsId = nomainit_sludinajumu($savienojums, $vecaisVeids, $id, $veids, $sqlTeikums, $params, $ipasniekaId);
                    $id = $jaunsId;

                    atjauninat_adresi($savienojums, $veids, $id, $pilseta, $iela, $majasNumurs);
                    if ($nomainitAtteli === "ja") {
                        atjauninat_attelus($savienojums, $atteli, $veids, $id);
                    }

                    if ($loma === "Admin") {
                        echo "Māja pārdošānai veiksmīgs atjaunināts!";
                    } else {
                        echo "Māja pārdošānai veiksmīgs atjaunināts! Lūdzu, gaidiet, kad administrācija atkal apstiprinās šo sludinājumu.";
                    }
                } else {
                    // Ja tips nav mainījies - vienkārši atjauniniet datus
                    $vaicajums = $savienojums->prepare("UPDATE majuvieta_pirkt SET cena = ?, platiba = ?, zemes_platiba = ?, istabas = ?, stavs_vai_stavi = ?, apraksts = ?, atjauninasanas_datums = ?, statuss = ?, ip_adrese = ? WHERE pirkt_id = ?");
                    $vaicajums->bind_param("iiiisssssi", $cenaPirkt, $platiba, $zemesPlatiba, $istabas, $stavs_vai_stavi, $apraksts, $datums, $statuss, $ip_adrese, $id);
                    if ($vaicajums->execute()) {
                        // echo "Māja pārdošānai veiksmīgs atjaunināts!";
                    } else {
                        // echo "Kļūda: " . $savienojums->error;
                    }
                    $vaicajums->close();

                    atjauninat_adresi($savienojums, $veids, $id, $pilseta, $iela, $majasNumurs);
                    if ($nomainitAtteli === "ja") {
                        atjauninat_attelus($savienojums, $atteli, $veids, $id);
                    }

                    if ($loma === "Admin") {
                        echo "Māja pārdošānai veiksmīgs atjaunināts!";
                    } else {
                        echo "Māja pārdošānai veiksmīgs atjaunināts! Lūdzu, gaidiet, kad administrācija atkal apstiprinās šo sludinājumu.";
                    }
                }
                //endregion
            } elseif ($veids === 'iret' && !empty($cenaDiena) && !empty($cenaNedela) && !empty($cenaMenesi) && $cenaDiena >= 1 && $cenaNedela >= 1 && $cenaMenesi >= 1) {
                //region Māja īrēšanai
                if ($vai_veids_ir_nomainits) {
                    $sqlTeikums = "INSERT INTO majuvieta_iret (id_ipasnieks, cena_diena, cena_nedela, cena_menesis, platiba, zemes_platiba, istabas, stavs_vai_stavi, apraksts, atjauninasanas_datums, statuss, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $ipasniekaId = iegut_ipasnieka_id($savienojums, $vecaisVeids, $id);
                    if ($ipasniekaId === 0) {
                        echo "Neizdevās noteikt īpašnieku!";
                        exit;
                    }

                    $params = ["iiiiiiisssss", $ipasniekaId, $cenaDiena, $cenaNedela, $cenaMenesi, $platiba, $zemesPlatiba, $istabas, $stavs_vai_stavi, $apraksts, $datums, $statuss, $ip_adrese];

                    $jaunsId = nomainit_sludinajumu($savienojums, $vecaisVeids, $id, $veids, $sqlTeikums, $params, $ipasniekaId);
                    $id = $jaunsId;

                    atjauninat_adresi($savienojums, $veids, $id, $pilseta, $iela, $majasNumurs);
                    if ($nomainitAtteli === "ja") {
                        atjauninat_attelus($savienojums, $atteli, $veids, $id);
                    }

                    if ($loma === "Admin") {
                        echo "Māja īrēšanai veiksmīgs atjaunināts!";
                    } else {
                        echo "Māja īrēšanai veiksmīgs atjaunināts! Lūdzu, gaidiet, kad administrācija atkal apstiprinās šo sludinājumu.";
                    }
                } else {
                    // Ja tips nav mainījies - vienkārši atjauniniet datus
                    $vaicajums = $savienojums->prepare("UPDATE majuvieta_iret SET cena_diena = ?, cena_nedela = ?, cena_menesis = ?, platiba = ?, zemes_platiba = ?, istabas = ?, stavs_vai_stavi = ?, apraksts = ?, atjauninasanas_datums = ?, statuss = ? WHERE iret_id = ?");
                    $vaicajums->bind_param("iiiiiissssi", $cenaDiena, $cenaNedela, $cenaMenesi, $platiba, $zemesPlatiba, $istabas, $stavs_vai_stavi, $apraksts, $datums, $statuss, $id);
                    if ($vaicajums->execute()) {
                        // echo "Māja pārdošānai veiksmīgs atjaunināts!";
                    } else {
                        // echo "Kļūda: " . $savienojums->error;
                    }
                    $vaicajums->close();

                    atjauninat_adresi($savienojums, $veids, $id, $pilseta, $iela, $majasNumurs);
                    if ($nomainitAtteli === "ja") {
                        atjauninat_attelus($savienojums, $atteli, $veids, $id);
                    }

                    if ($loma === "Admin") {
                        echo "Māja īrēšanai veiksmīgs atjaunināts!";
                    } else {
                        echo "Māja īrēšanai veiksmīgs atjaunināts! Lūdzu, gaidiet, kad administrācija atkal apstiprinās šo sludinājumu.";
                    }
                }
                //endregion
            } else {
                echo "Visi ievadas lauki nav aizpildīti!";
            }
        } elseif ($tips === 'dzivoklis' && !empty($dzivoklaNumurs)) {
            if ($veids === 'pirkt' && !empty($cenaPirkt) && $cenaPirkt >= 1) {
                //region Dzīvoklis pārdošanai
                if ($vai_veids_ir_nomainits) {
                    $sqlTeikums = "INSERT INTO majuvieta_pirkt (majokla_tips, id_ipasnieks, cena, platiba, istabas, stavs_vai_stavi, apraksts, atjauninasanas_datums, statuss, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $ipasniekaId = iegut_ipasnieka_id($savienojums, $vecaisVeids, $id);
                    if ($ipasniekaId === 0) {
                        echo "Neizdevās noteikt īpašnieku!";
                        exit;
                    }

                    $params = ["siiiisssss", $tips, $ipasniekaId, $cenaPirkt, $platiba, $istabas, $stavs_vai_stavi, $apraksts, $datums, $statuss, $ip_adrese];

                    $jaunsId = nomainit_sludinajumu($savienojums, $vecaisVeids, $id, $veids, $sqlTeikums, $params, $ipasniekaId);
                    $id = $jaunsId;

                    atjauninat_adresi($savienojums, $veids, $id, $pilseta, $iela, $majasNumurs, $dzivoklaNumurs);
                    if ($nomainitAtteli === "ja") {
                        atjauninat_attelus($savienojums, $atteli, $veids, $id);
                    }

                    if ($loma === "Admin") {
                        echo "Dzīvoklis pārdošānai veiksmīgs atjaunināts!";
                    } else {
                        echo "Dzīvoklis pārdošānai veiksmīgs atjaunināts! Lūdzu, gaidiet, kad administrācija atkal apstiprinās šo sludinājumu.";
                    }
                } else {
                    // Ja tips nav mainījies - vienkārši atjauniniet datus
                    $vaicajums = $savienojums->prepare("UPDATE majuvieta_pirkt SET cena = ?, platiba = ?, istabas = ?, stavs_vai_stavi = ?, apraksts = ?, atjauninasanas_datums = ?, statuss = ? WHERE pirkt_id = ?");
                    $vaicajums->bind_param("iiissssi", $cenaPirkt, $platiba, $istabas, $stavs_vai_stavi, $apraksts, $datums, $statuss, $id);
                    if ($vaicajums->execute()) {
                        // echo "Māja pārdošānai veiksmīgs atjaunināts!";
                    } else {
                        // echo "Kļūda: " . $savienojums->error;
                    }
                    $vaicajums->close();

                    atjauninat_adresi($savienojums, $veids, $id, $pilseta, $iela, $majasNumurs, $dzivoklaNumurs);
                    if ($nomainitAtteli === "ja") {
                        atjauninat_attelus($savienojums, $atteli, $veids, $id);
                    }

                    if ($loma === "Admin") {
                        echo "Dzīvoklis pārdošānai veiksmīgs atjaunināts!";
                    } else {
                        echo "Dzīvoklis pārdošānai veiksmīgs atjaunināts! Lūdzu, gaidiet, kad administrācija atkal apstiprinās šo sludinājumu.";
                    }
                }
                //endregion
            } elseif ($veids === 'iret' && !empty($cenaDiena) && !empty($cenaNedela) && !empty($cenaMenesi) && $cenaDiena >= 1 && $cenaNedela >= 1 && $cenaMenesi >= 1) {
                //region Dzīvoklis īrēšanai
                if ($vai_veids_ir_nomainits) {
                    $sqlTeikums = "INSERT INTO majuvieta_iret (majokla_tips, id_ipasnieks, cena_diena, cena_nedela, cena_menesis, platiba, istabas, stavs_vai_stavi, apraksts, atjauninasanas_datums, statuss, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $ipasniekaId = iegut_ipasnieka_id($savienojums, $vecaisVeids, $id);
                    if ($ipasniekaId === 0) {
                        echo "Neizdevās noteikt īpašnieku!";
                        exit;
                    }

                    $params = ["siiiiiisssss", $tips, $ipasniekaId, $cenaDiena, $cenaNedela, $cenaMenesi, $platiba, $istabas, $stavs_vai_stavi, $apraksts, $datums, $statuss, $ip_adrese];

                    $jaunsId = nomainit_sludinajumu($savienojums, $vecaisVeids, $id, $veids, $sqlTeikums, $params, $ipasniekaId);
                    $id = $jaunsId;

                    atjauninat_adresi($savienojums, $veids, $id, $pilseta, $iela, $majasNumurs, $dzivoklaNumurs);
                    if ($nomainitAtteli === "ja") {
                        atjauninat_attelus($savienojums, $atteli, $veids, $id);
                    }

                    if ($loma === "Admin") {
                        echo "Dzīvoklis īrēšanai veiksmīgs atjaunināts!";
                    } else {
                        echo "Dzīvoklis īrēšanai veiksmīgs atjaunināts! Lūdzu, gaidiet, kad administrācija atkal apstiprinās šo sludinājumu.";
                    }
                } else {
                    // Ja tips nav mainījies - vienkārši atjauniniet datus
                    $vaicajums = $savienojums->prepare("UPDATE majuvieta_iret SET cena_diena = ?, cena_nedela = ?, cena_menesis = ?, platiba = ?, istabas = ?, stavs_vai_stavi = ?, apraksts = ?, atjauninasanas_datums = ?, statuss = ? WHERE iret_id = ?");
                    $vaicajums->bind_param("iiiiissssi", $cenaDiena, $cenaNedela, $cenaMenesi, $platiba, $istabas, $stavs_vai_stavi, $apraksts, $datums, $statuss, $id);
                    if ($vaicajums->execute()) {
                        // echo "Māja pārdošānai veiksmīgs atjaunināts!";
                    } else {
                        // echo "Kļūda: " . $savienojums->error;
                    }
                    $vaicajums->close();

                    atjauninat_adresi($savienojums, $veids, $id, $pilseta, $iela, $majasNumurs, $dzivoklaNumurs);
                    if ($nomainitAtteli === "ja") {
                        atjauninat_attelus($savienojums, $atteli, $veids, $id);
                    }

                    if ($loma === "Admin") {
                        echo "Dzīvoklis īrēšanai veiksmīgs atjaunināts!";
                    } else {
                        echo "Dzīvoklis īrēšanai veiksmīgs atjaunināts! Lūdzu, gaidiet, kad administrācija atkal apstiprinās šo sludinājumu.";
                    }
                }
                //endregion
            } else {
                echo "Visi ievadas lauki nav aizpildīti!";
            }
        } else {
            echo "Visi ievadas lauki nav aizpildīti!";
        }
    } else {
        echo "Visi ievadas lauki nav aizpildīti!";
    }
    $savienojums->close();
} else {
    echo "Kļūda!";
}
