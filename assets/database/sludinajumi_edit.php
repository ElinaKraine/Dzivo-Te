<?php
require '../../admin/database/con_db.php';
session_start();

function process_images_update($files)
{
    $result = array_fill(0, 10, null);
    foreach ($files['tmp_name'] as $i => $tmp) {
        if ($i >= 10) break;
        if (filesize($tmp) <= 200 * 1024) {
            $result[$i] = file_get_contents($tmp);
        }
    }
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $datums = date("Y-m-d H:i:s");

    $cenaPirkt = $_POST['cenaPirkt'] ?? null;
    $cenaDiena = $_POST['cenaDiena'] ?? null;
    $cenaNedela = $_POST['cenaNedela'] ?? null;
    $cenaMenesi = $_POST['cenaMenesi'] ?? null;

    $nomainitAtteli = $_POST['nomainitAtteli'] ?? 'ne';
    $statuss = "Iesniegts sludinājums";
    $lietotajaId = $_SESSION['lietotajaIdDt'];

    // Esošā (vecā) tipa noteikšana
    $oldVeids = null;
    $stmt = $savienojums->prepare("SELECT 1 FROM majuvieta_pirkt WHERE pirkt_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $oldVeids = "pirkt";
    }
    $stmt->close();

    if (!$oldVeids) {
        $stmt = $savienojums->prepare("SELECT 1 FROM majuvieta_iret WHERE iret_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $oldVeids = "iret";
        }
        $stmt->close();
    }

    if (!$oldVeids) {
        echo "Nevar atrast sludinājumu datubāzē.";
        exit;
    }

    // Rediģēšanas aizliegums, ja ir pieteikumi/rezervācijas
    if ($oldVeids === "pirkt") {
        $stmt = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_pieteikumi WHERE id_majuvieta_pirkt = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            echo "Šo sludinājumu nevar rediģēt, jo tam jau ir pieteikumi.";
            exit;
        }
    } else {
        $today = date("Y-m-d");
        $stmt = $savienojums->prepare("SELECT COUNT(*) FROM majuvieta_iziresana WHERE id_majuvieta_iret = ? AND izrakstisanas_datums >= ?");
        $stmt->bind_param("is", $id, $today);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            echo "Šo sludinājumu nevar rediģēt, jo tam jau ir rezervācijas.";
            exit;
        }
    }

    $savienojums->begin_transaction();
    try {
        $changingType = $oldVeids !== $veids;

        if ($changingType) {
            // Dzēšana no vecas tabulas
            $oldTabula = $oldVeids === "pirkt" ? "majuvieta_pirkt" : "majuvieta_iret";
            $idField = $oldVeids === "pirkt" ? "pirkt_id" : "iret_id";
            $stmt = $savienojums->prepare("DELETE FROM $oldTabula WHERE $idField = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Ievietošana jaunā tabulā
            if ($veids === "pirkt") {
                $stmt = $savienojums->prepare("INSERT INTO majuvieta_pirkt (id_ipasnieks, cena, platiba, zemes_platiba, istabas, stavs_vai_stavi, apraksts, atjauninasanas_datums, statuss)
                                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iiiiissss", $lietotajaId, $cenaPirkt, $platiba, $zemesPlatiba, $istabas, $stavs_vai_stavi, $apraksts, $datums, $statuss);
            } else {
                $stmt = $savienojums->prepare("INSERT INTO majuvieta_iret (id_ipasnieks, cena_diena, cena_nedela, cena_menesis, platiba, zemes_platiba, istabas, stavs_vai_stavi, apraksts, atjauninasanas_datums, statuss)
                                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iiiiiiissss", $lietotajaId, $cenaDiena, $cenaNedela, $cenaMenesi, $platiba, $zemesPlatiba, $istabas, $stavs_vai_stavi, $apraksts, $datums, $statuss);
            }

            if (!$stmt->execute()) throw new Exception("Kļūda, mainot veidu: " . $stmt->error);
            $newId = $stmt->insert_id;
            $stmt->close();

            // Veids un ID atjaunināšana tabulās majuvieta_adrese un majuvieta_atteli
            foreach (['majuvieta_adrese', 'majuvieta_atteli'] as $tabula) {
                $sql = "UPDATE $tabula SET sludinajuma_veids = ?, id_sludinajums = ? WHERE sludinajuma_veids = ? AND id_sludinajums = ?";
                $stmt = $savienojums->prepare($sql);
                $stmt->bind_param("sisi", $veids, $newId, $oldVeids, $id);
                $stmt->execute();
                $stmt->close();
            }

            $id = $newId;
        } else {
            // Ja tips nav mainījies - vienkārši atjauniniet datus
            $tabula = $veids === "pirkt" ? "majuvieta_pirkt" : "majuvieta_iret";
            if ($veids === "pirkt") {
                $stmt = $savienojums->prepare("UPDATE $tabula SET cena = ?, platiba = ?, zemes_platiba = ?, istabas = ?, stavs_vai_stavi = ?, apraksts = ?, atjauninasanas_datums = ?, statuss = ? WHERE pirkt_id = ?");
                $stmt->bind_param("iiiissssi", $cenaPirkt, $platiba, $zemesPlatiba, $istabas, $stavs_vai_stavi, $apraksts, $datums, $statuss, $id);
            } else {
                $stmt = $savienojums->prepare("UPDATE $tabula SET cena_diena = ?, cena_nedela = ?, cena_menesis = ?, platiba = ?, zemes_platiba = ?, istabas = ?, stavs_vai_stavi = ?, apraksts = ?, atjauninasanas_datums = ?, statuss = ? WHERE iret_id = ?");
                $stmt->bind_param("iiiiiissssi", $cenaDiena, $cenaNedela, $cenaMenesi, $platiba, $zemesPlatiba, $istabas, $stavs_vai_stavi, $apraksts, $datums, $statuss, $id);
            }
            if (!$stmt->execute()) throw new Exception("Kļūda datu atjaunināšanā: " . $stmt->error);
            $stmt->close();
        }

        // Adreses atjaunināšana
        if ($tips === "dzivoklis") {
            $stmt = $savienojums->prepare("UPDATE majuvieta_adrese SET pilseta = ?, iela = ?, majas_numurs = ?, dzivokla_numurs = ? WHERE sludinajuma_veids = ? AND id_sludinajums = ?");
            $stmt->bind_param("sssssi", $pilseta, $iela, $majasNumurs, $dzivoklaNumurs, $veids, $id);
        } else {
            $stmt = $savienojums->prepare("UPDATE majuvieta_adrese SET pilseta = ?, iela = ?, majas_numurs = ?, dzivokla_numurs = NULL WHERE sludinajuma_veids = ? AND id_sludinajums = ?");
            $stmt->bind_param("ssssi", $pilseta, $iela, $majasNumurs, $veids, $id);
        }
        if (!$stmt->execute()) throw new Exception("Kļūda adreses atjaunināšanā: " . $stmt->error);
        $stmt->close();

        // Attēlu atjaunināšana
        if ($nomainitAtteli === "ja" && isset($_FILES['atteli'])) {
            $encoded = process_images_update($_FILES['atteli']);
            $columns = [
                "pirma_attela",
                "otra_attela",
                "tresa_attela",
                "ceturta_attela",
                "piekta_attela",
                "sesta_attela",
                "septita_attela",
                "astota_attela",
                "devita_attela",
                "desmita_attela"
            ];
            $set = implode(" = ?, ", $columns) . " = ?";
            $sql = "UPDATE majuvieta_atteli SET $set WHERE sludinajuma_veids = ? AND id_sludinajums = ?";
            $stmt = $savienojums->prepare($sql);
            $types = str_repeat("s", 10) . "si";
            $params = array_merge($encoded, [$veids, $id]);
            $stmt->bind_param($types, ...$params);
            if (!$stmt->execute()) throw new Exception("Kļūda attēlu atjaunināšanā: " . $stmt->error);
            $stmt->close();
        }

        $savienojums->commit();
        echo "Dati veiksmīgi atjaunināti.";
    } catch (Exception $e) {
        $savienojums->rollback();
        echo "Kļūda: " . $e->getMessage();
    }

    $savienojums->close();
} else {
    echo "Nederīgs pieprasījums.";
}
