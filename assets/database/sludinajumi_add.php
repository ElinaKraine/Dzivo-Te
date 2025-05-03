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

function process_images($files)
{
    $encoded = array_fill(0, 10, null);
    foreach ($files['tmp_name'] as $i => $tmp) {
        if ($i >= 10) break;
        if (filesize($tmp) <= 200 * 1024) {
            $encoded[$i] = file_get_contents($tmp);
        }
    }
    return $encoded;
}

if (!empty($majokla_tips) && !empty($majokla_veids) && !empty($pilseta) && !empty($iela) && !empty($majas_numurs) && !empty($platiba)) {
    $savienojums->begin_transaction();
    try {
        if ($majokla_tips === 'maja') {
            $tips = "Mājas";
            if (!empty($zemes_platiba) && !empty($stavi)) {
                if ($majokla_veids === 'pirkt' && !empty($cenaPirkt) && $cenaPirkt >= 1) {
                    // Māja pārdošanai
                    $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_pirkt (majokla_tips, id_ipasnieks, cena, platiba, zemes_platiba, istabas, stavs_vai_stavi, apraksts, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $vaicajums->bind_param("siiiiisss", $tips, $lietotajaId, $cenaPirkt, $platiba, $zemes_platiba, $istabas, $stavi, $apraksts, $ip_adrese);
                    if (!$vaicajums->execute()) throw new Exception($vaicajums->error);
                    $sludinajuma_id = $vaicajums->insert_id;
                    $sludinajuma_veids = "Pirkt";

                    $stmt = $savienojums->prepare("INSERT INTO majuvieta_adrese (pilseta, iela, majas_numurs, sludinajuma_veids, id_sludinajums) VALUES (?, ?, ?, ?, ?)");
                    if (!$stmt) throw new Exception($savienojums->error);
                    $stmt->bind_param("ssssi", $pilseta, $iela, $majas_numurs, $sludinajuma_veids, $sludinajuma_id);
                    if (!$stmt->execute()) throw new Exception($stmt->error);

                    $sql = $savienojums->prepare("INSERT INTO majuvieta_atteli (
                        pirma_attela, otra_attela, tresa_attela, ceturta_attela, piekta_attela,
                        sesta_attela, septita_attela, astota_attela, devita_attela, desmita_attela,
                        sludinajuma_veids, id_sludinajums
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    if (!$sql) throw new Exception($savienojums->error);

                    $encoded = process_images($_FILES['atteli']);
                    $parametruTips = str_repeat("s", 10) . "si";
                    $visiParametri = array_merge($encoded, [$sludinajuma_veids, $sludinajuma_id]);
                    $sql->bind_param($parametruTips, ...$visiParametri);
                    if (!$sql->execute()) throw new Exception($sql->error);

                    $savienojums->commit();
                    $_SESSION['pazinojums'] = "Sludinājums veiksmīgi izveidots!";
                } elseif ($majokla_veids === 'iret' && !empty($cenaDiena) && !empty($cenaNedela) && !empty($cenaMenesi) && $cenaDiena >= 1 && $cenaNedela >= 1 && $cenaMenesi >= 1) {
                    // Māja īrēšanai
                    $tips = "Mājas";
                    $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_iret (majokla_tips, id_ipasnieks, cena_diena, cena_nedela, cena_menesis, platiba, zemes_platiba, istabas, stavs_vai_stavi, apraksts, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $vaicajums->bind_param("siiiiiiisss", $tips, $lietotajaId, $cenaDiena, $cenaNedela, $cenaMenesi, $platiba, $zemes_platiba, $istabas, $stavi, $apraksts, $ip_adrese);
                    if (!$vaicajums->execute()) throw new Exception($vaicajums->error);
                    $sludinajuma_id = $vaicajums->insert_id;
                    $sludinajuma_veids = "Iret";

                    $stmt = $savienojums->prepare("INSERT INTO majuvieta_adrese (pilseta, iela, majas_numurs, sludinajuma_veids, id_sludinajums) VALUES (?, ?, ?, ?, ?)");
                    if (!$stmt) throw new Exception($savienojums->error);
                    $stmt->bind_param("ssssi", $pilseta, $iela, $majas_numurs, $sludinajuma_veids, $sludinajuma_id);
                    if (!$stmt->execute()) throw new Exception($stmt->error);

                    $sql = $savienojums->prepare("INSERT INTO majuvieta_atteli (
                        pirma_attela, otra_attela, tresa_attela, ceturta_attela, piekta_attela,
                        sesta_attela, septita_attela, astota_attela, devita_attela, desmita_attela,
                        sludinajuma_veids, id_sludinajums
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    if (!$sql) throw new Exception($savienojums->error);

                    $encoded = process_images($_FILES['atteli']);
                    $parametruTips = str_repeat("s", 10) . "si";
                    $visiParametri = array_merge($encoded, [$sludinajuma_veids, $sludinajuma_id]);
                    $sql->bind_param($parametruTips, ...$visiParametri);
                    if (!$sql->execute()) throw new Exception($sql->error);

                    $savienojums->commit();
                    $_SESSION['pazinojums'] = "Sludinājums veiksmīgi izveidots!";
                } else {
                    $_SESSION['pazinojums'] = "Nepareizi norādītas cenas vai trūkst datu!";
                }
            } else {
                $_SESSION['pazinojums'] = "Trūkst zemes platības vai stāvu skaita!";
            }
        } elseif ($majokla_tips === 'dzivoklis') {
            $tips = "Dzīvoklis";
            if (!empty($dzivokla_numurs) && !empty($stavs)) {
                if ($majokla_veids === 'pirkt' && !empty($cenaPirkt) && $cenaPirkt >= 1) {
                    // Dzīvoklis pārdošanai
                    $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_pirkt (majokla_tips, id_ipasnieks, cena, platiba, istabas, stavs_vai_stavi, apraksts, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $vaicajums->bind_param("siiiisss", $tips, $lietotajaId, $cenaPirkt, $platiba, $istabas, $stavs, $apraksts, $ip_adrese);
                    if (!$vaicajums->execute()) throw new Exception($vaicajums->error);
                    $sludinajuma_id = $vaicajums->insert_id;
                    $sludinajuma_veids = "Pirkt";

                    $stmt = $savienojums->prepare("INSERT INTO majuvieta_adrese (pilseta, iela, majas_numurs, dzivokla_numurs, sludinajuma_veids, id_sludinajums) VALUES (?, ?, ?, ?, ?, ?)");
                    if (!$stmt) throw new Exception($savienojums->error);
                    $stmt->bind_param("sssssi", $pilseta, $iela, $majas_numurs, $dzivokla_numurs, $sludinajuma_veids, $sludinajuma_id);
                    if (!$stmt->execute()) throw new Exception($stmt->error);

                    $sql = $savienojums->prepare("INSERT INTO majuvieta_atteli (
                        pirma_attela, otra_attela, tresa_attela, ceturta_attela, piekta_attela,
                        sesta_attela, septita_attela, astota_attela, devita_attela, desmita_attela,
                        sludinajuma_veids, id_sludinajums
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    if (!$sql) throw new Exception($savienojums->error);

                    $encoded = process_images($_FILES['atteli']);
                    $parametruTips = str_repeat("s", 10) . "si";
                    $visiParametri = array_merge($encoded, [$sludinajuma_veids, $sludinajuma_id]);
                    $sql->bind_param($parametruTips, ...$visiParametri);
                    if (!$sql->execute()) throw new Exception($sql->error);

                    $savienojums->commit();
                    $_SESSION['pazinojums'] = "Sludinājums veiksmīgi izveidots!";
                } elseif ($majokla_veids === 'iret' && !empty($cenaDiena) && !empty($cenaNedela) && !empty($cenaMenesi) && $cenaDiena >= 1 && $cenaNedela >= 1 && $cenaMenesi >= 1) {
                    // Dzīvoklis īrēšanai
                    $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_iret (majokla_tips, id_ipasnieks, cena_diena, cena_nedela, cena_menesis, platiba, istabas, stavs_vai_stavi, apraksts, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $vaicajums->bind_param("siiiiiisss", $tips, $lietotajaId, $cenaDiena, $cenaNedela, $cenaMenesi, $platiba, $istabas, $stavs, $apraksts, $ip_adrese);
                    if (!$vaicajums->execute()) throw new Exception($vaicajums->error);

                    $sludinajuma_id = $vaicajums->insert_id;
                    $sludinajuma_veids = "Iret";

                    $stmt = $savienojums->prepare("INSERT INTO majuvieta_adrese (pilseta, iela, majas_numurs, dzivokla_numurs, sludinajuma_veids, id_sludinajums) VALUES (?, ?, ?, ?, ?, ?)");
                    if (!$stmt) throw new Exception($savienojums->error);
                    $stmt->bind_param("sssssi", $pilseta, $iela, $majas_numurs, $dzivokla_numurs, $sludinajuma_veids, $sludinajuma_id);
                    if (!$stmt->execute()) throw new Exception($stmt->error);

                    $sql = $savienojums->prepare("INSERT INTO majuvieta_atteli (
                        pirma_attela, otra_attela, tresa_attela, ceturta_attela, piekta_attela,
                        sesta_attela, septita_attela, astota_attela, devita_attela, desmita_attela,
                        sludinajuma_veids, id_sludinajums
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    if (!$sql) throw new Exception($savienojums->error);

                    $encoded = process_images($_FILES['atteli']);
                    $parametruTips = str_repeat("s", 10) . "si";
                    $visiParametri = array_merge($encoded, [$sludinajuma_veids, $sludinajuma_id]);
                    $sql->bind_param($parametruTips, ...$visiParametri);
                    if (!$sql->execute()) throw new Exception($sql->error);

                    $savienojums->commit();
                    $_SESSION['pazinojums'] = "Sludinājums veiksmīgi izveidots!";
                } else {
                    $_SESSION['pazinojums'] = "Nepareizi norādītas cenas vai trūkst datu!";
                }
            } else {
                $_SESSION['pazinojums'] = "Trūkst dzīvokļa numura vai stāva!";
            }
        }
    } catch (Exception $e) {
        $savienojums->rollback();
        $_SESSION['pazinojums'] = "Kļūda: " . $e->getMessage();
    }
} else {
    $_SESSION['pazinojums'] = "Visi ievadas lauki nav aizpildīti!";
}

exit;
