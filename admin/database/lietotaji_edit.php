<?php
require 'con_db.php';

if (isset($_POST['lietotajs_admin_ID'])) {
    $vards = htmlspecialchars($_POST['lietVardsTabulaAdmin']);
    $uzvards = htmlspecialchars($_POST['lietUzvardsTabulaAdmin']);
    $epasts = htmlspecialchars($_POST['lietEpastsTabulaAdmin']);
    $talrunis = htmlspecialchars($_POST['lietTalrunisTabulaAdmin']);
    $loma = htmlspecialchars($_POST['lomaSelect']);
    $id = intval($_POST['lietotajs_admin_ID']);
    $ip_adrese = $_SERVER['REMOTE_ADDR'];
    $tagad = date("Y-m-d H:i:s");

    $nomainitAttelu = $_POST['nomainitAtteluSelectTabulaAdmin'] ?? 'ne';
    $nomainitParole = $_POST['nomainitParoleSelectTabulaAdmin'] ?? 'ne';
    if ($nomainitParole === "ja") {
        $parole1 = mysqli_real_escape_string($savienojums, $_POST["lietParoleTabulaAdmin"]);
        $parole2 = mysqli_real_escape_string($savienojums, $_POST["lietParoleOtraisTabulaAdmin"]);
    }

    $vaicajums = "SELECT vards, uzvards FROM majuvieta_lietotaji WHERE vards = '$vards' AND uzvards = '$uzvards' AND lietotaja_id != '$id'";
    $rezultatsVardsUzvards = mysqli_query($savienojums, $vaicajums);

    $vaicajums = "SELECT epasts FROM majuvieta_lietotaji WHERE epasts = '$epasts' AND lietotaja_id != '$id'";
    $rezultatsEpasts = mysqli_query($savienojums, $vaicajums);

    $vaicajums = "SELECT talrunis FROM majuvieta_lietotaji WHERE talrunis = '$talrunis' AND lietotaja_id != '$id'";
    $rezultatsTalrunis = mysqli_query($savienojums, $vaicajums);

    $password_pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/";
    $attels_data = null;
    $paroleHash = null;

    if ($nomainitAttelu === "ja") {
        if (isset($_FILES['attelsTabulaAdmin']) && $_FILES['attelsTabulaAdmin']['error'] === UPLOAD_ERR_OK) {
            $attels_tmp = $_FILES['attelsTabulaAdmin']['tmp_name'];
            $attels_data = file_get_contents($attels_tmp);
        } else {
            echo "Visi ievadas lauki nav aizpildīti!";
            exit;
        }
    }

    if ($nomainitParole === "ja") {
        if (!empty($parole1) && !empty($parole2)) {
            if ($parole1 !== $parole2) {
                echo "Paroles nesakrīt!";
                exit;
            } elseif (!preg_match($password_pattern, $parole1)) {
                echo "Parole jābūt vismaz 8 rakstzīmēm, ar vismaz vienu mazo burtu, vienu lielo burtu un skaitli!";
                exit;
            } else {
                $paroleHash = password_hash($parole1, PASSWORD_DEFAULT);
            }
        } else {
            echo "Visi ievadas lauki nav aizpildīti!";
            exit;
        }
    }

    if (!empty($vards) && !empty($uzvards) && !empty($epasts) && !empty($talrunis)) {
        if (mysqli_num_rows($rezultatsEpasts) > 0) {
            echo "Šis e-pasts jau eksistē!";
        } else if (mysqli_num_rows($rezultatsVardsUzvards) > 0) {
            echo "Lietotājs ar šo vārdu un uzvārdu jau eksistē!";
        } else if (mysqli_num_rows($rezultatsTalrunis) > 0) {
            echo "Šis tālrunis jau eksistē!";
        } else {
            $sql_teikums = "UPDATE majuvieta_lietotaji SET vards = ?, uzvards = ?, epasts = ?, talrunis = ?, loma = ?, ";
            $params = [$vards, $uzvards, $epasts, $talrunis, $loma];
            $types = "sssss";

            if ($attels_data !== null) {
                $sql_teikums .= "attels = ?, ";
                $params[] = $attels_data;
                $types .= "s";
            }

            if ($paroleHash !== null) {
                $sql_teikums .= "parole = ?, ";
                $params[] = $paroleHash;
                $types .= "s";
            }

            $sql_teikums .= "atjauninasanas_datums = ?, ip_adrese = ? WHERE lietotaja_id = ?";
            $params[] = $tagad;
            $params[] = $ip_adrese;
            $params[] = $id;
            $types .= "ssi";

            $vaicajums = $savienojums->prepare($sql_teikums);
            $vaicajums->bind_param($types, ...$params);

            if ($vaicajums->execute()) {
                echo "Lietotāja informācija ir veiksmīgi rediģēta!";
            } else {
                // echo "Kļūda: " . $savienojums->error;
            }
        }
    } else {
        echo "Visi ievadas lauki nav aizpildīti!";
    }

    $savienojums->close();
} else {
    echo "Kļūda";
}
