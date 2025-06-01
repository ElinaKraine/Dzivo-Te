<?php
require '../../admin/database/con_db.php';

if (isset($_POST['liet_ID']) || isset($_POST['liet_admin_ID'])) {
    $ip_adrese = $_SERVER['REMOTE_ADDR'];

    if (isset($_POST['liet_ID'])) {
        $vards = htmlspecialchars($_POST['lietVards']);
        $uzvards = htmlspecialchars($_POST['lietUzvards']);
        $epasts = htmlspecialchars($_POST['lietEpasts']);
        $talrunis = htmlspecialchars($_POST['lietTalrunis']);
        $id = intval($_POST['liet_ID']);
        $nomainitAttelu = $_POST['nomainitAttelu'] ?? 'ne';
        $nomainitParole = $_POST['nomainitParole'] ?? 'ne';
        if ($nomainitParole === "ja") {
            $parole1 = mysqli_real_escape_string($savienojums, $_POST["lietParole"]);
            $parole2 = mysqli_real_escape_string($savienojums, $_POST["lietParoleOtrais"]);
        }
    } else {
        $vards = htmlspecialchars($_POST['lietVardsAdmin']);
        $uzvards = htmlspecialchars($_POST['lietUzvardsAdmin']);
        $epasts = htmlspecialchars($_POST['lietEpastsAdmin']);
        $talrunis = htmlspecialchars($_POST['lietTalrunisAdmin']);
        $id = intval($_POST['liet_admin_ID']);
        $nomainitAttelu = $_POST['nomainitAtteluAdmin'] ?? 'ne';
        $nomainitParole = $_POST['nomainitParoleAdmin'] ?? 'ne';
        if ($nomainitParole === "ja") {
            $parole1 = mysqli_real_escape_string($savienojums, $_POST["lietParoleAdmin"]);
            $parole2 = mysqli_real_escape_string($savienojums, $_POST["lietParoleOtraisAdmin"]);
        }
    }

    $vaicajums = "SELECT vards, uzvards FROM majuvieta_lietotaji WHERE vards = '$vards' AND uzvards = '$uzvards' AND lietotaja_id != '$id' AND statuss != 'Dzēsts'";
    $rezultatsVardsUzvards = mysqli_query($savienojums, $vaicajums);

    $vaicajums = "SELECT epasts FROM majuvieta_lietotaji WHERE epasts = '$epasts' AND lietotaja_id != '$id' AND statuss != 'Dzēsts'";
    $rezultatsEpasts = mysqli_query($savienojums, $vaicajums);

    $vaicajums = "SELECT talrunis FROM majuvieta_lietotaji WHERE talrunis = '$talrunis' AND lietotaja_id != '$id' AND statuss != 'Dzēsts'";
    $rezultatsTalrunis = mysqli_query($savienojums, $vaicajums);

    $password_pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/";
    $tagad = date("Y-m-d H:i:s");

    $attels_data = null;
    $paroleHash = null;

    if ($nomainitAttelu === "ja") {
        if (isset($_FILES['attels']) && $_FILES['attels']['error'] === UPLOAD_ERR_OK) {
            $attels_tmp = $_FILES['attels']['tmp_name'];
            $attels_data = file_get_contents($attels_tmp);
        } elseif (isset($_FILES['attelsAdmin']) && $_FILES['attelsAdmin']['error'] === UPLOAD_ERR_OK) {
            $attels_tmp = $_FILES['attelsAdmin']['tmp_name'];
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
            } elseif (!preg_match($password_pattern, $parole1)) {
                echo "Parole jābūt vismaz 8 rakstzīmēm, ar vismaz vienu mazo burtu, vienu lielo burtu un skaitli!";
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
            $sql = "UPDATE majuvieta_lietotaji SET vards = ?, uzvards = ?, epasts = ?, talrunis = ?,";
            $params = [$vards, $uzvards, $epasts, $talrunis];
            $types = "ssss";

            if ($attels_data !== null) {
                $sql .= "attels = ?, ";
                $params[] = $attels_data;
                $types .= "s";
            }

            if ($paroleHash !== null) {
                $sql .= "parole = ?, ";
                $params[] = $paroleHash;
                $types .= "s";
            }

            $sql .= "atjauninasanas_datums = ?, ip_adrese = ? WHERE lietotaja_id = ?";
            $params[] = $tagad;
            $params[] = $ip_adrese;
            $params[] = $id;
            $types .= "ssi";

            $vaicajums = $savienojums->prepare($sql);
            $vaicajums->bind_param($types, ...$params);

            if ($vaicajums->execute()) {
                echo "Profila informācija ir veiksmīgi rediģēta!";
            } else {
                // echo "Kļūda!";
            }
        }
    } else {
        echo "Visi ievadas lauki nav aizpildīti!";
    }

    $savienojums->close();
} else {
    echo "Kļūda!";
}
