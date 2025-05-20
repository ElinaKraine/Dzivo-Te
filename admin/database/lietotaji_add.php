<?php
require 'con_db.php';
session_start();

$loma = $_SESSION['lietotajaLomaMV'] === 'Administrators' ? htmlspecialchars($_POST['lomaSelect']) : 'Lietotājs';
$attels = $_FILES['attelsTabulaAdmin'];
$ip_adrese = $_SERVER['REMOTE_ADDR'];

$epasts = htmlspecialchars($_POST['lietEpastsTabulaAdmin']);
$vaicajums = "SELECT epasts FROM majuvieta_lietotaji WHERE epasts = '$epasts'";
$rezultatsEpasts = mysqli_query($savienojums, $vaicajums);

$vards = htmlspecialchars($_POST['lietVardsTabulaAdmin']);
$uzvards = htmlspecialchars($_POST['lietUzvardsTabulaAdmin']);
$vaicajums = "SELECT vards, uzvards FROM majuvieta_lietotaji WHERE vards = '$vards' AND uzvards = '$uzvards'";
$rezultatsVardsUzvards = mysqli_query($savienojums, $vaicajums);

$talrunis = htmlspecialchars($_POST['lietTalrunisTabulaAdmin']);
$vaicajums = "SELECT talrunis FROM majuvieta_lietotaji WHERE talrunis = '$talrunis'";
$rezultatsTalrunis = mysqli_query($savienojums, $vaicajums);

$parole = htmlspecialchars($_POST['lietParoleTabulaAdmin']);
$paroleAtkartoti = htmlspecialchars($_POST['lietParoleOtraisTabulaAdmin']);
$password_pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/";

if (!empty($vards) && !empty($uzvards) && !empty($epasts) && !empty($talrunis) && !empty($parole) && !empty($paroleAtkartoti) && !empty($loma)) {
    if (mysqli_num_rows($rezultatsEpasts) > 0) {
        $_SESSION['pazinojumsMV'] = "Šis e-pasts jau eksistē!";
    } else if (mysqli_num_rows($rezultatsVardsUzvards) > 0) {
        $_SESSION['pazinojumsMV'] = "Lietotājs ar šo vārdu un uzvārdu jau eksistē!";
    } else if (mysqli_num_rows($rezultatsTalrunis) > 0) {
        $_SESSION['pazinojumsMV'] = "Šis tālrunis jau eksistē!";
    } else if ($parole != $paroleAtkartoti) {
        $_SESSION['pazinojumsMV'] = "Paroli nav vienādi!";
    } else if (!preg_match($password_pattern, $parole)) {
        $_SESSION['pazinojumsMV'] = "Parole jābūt vismaz 8 rakstzīmēm, ar vismaz vienu mazo burtu, vienu lielo burtu un skaitli!";
    } else {
        if ($_FILES['attelsTabulaAdmin']['error'] === UPLOAD_ERR_OK) {
            $attels_tmp = $_FILES['attelsTabulaAdmin']['tmp_name'];
            $attels_data = file_get_contents($attels_tmp);
        } else {
            $_SESSION['pazinojumsMV'] = "Kaut kas nepareizi ar attēlai!";
        }

        $paroleHash = password_hash($parole, PASSWORD_DEFAULT);
        $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_lietotaji(vards, uzvards, epasts, talrunis, loma, parole, attels, ip_adrese) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $vaicajums->bind_param("ssssssss", $vards, $uzvards, $epasts, $talrunis, $loma, $paroleHash, $attels_data, $ip_adrese);
        if ($vaicajums->execute()) {
            $_SESSION['pazinojumsMV'] = "Lietotājs veiksmīgi pievienots!";
        } else {
            // echo "Kļūda: " . $vaicajums->error;
        }
        $vaicajums->close();
        $savienojums->close();
    }
} else {
    $_SESSION['pazinojumsMV'] = "Visi ievadas lauki nav aizpildīti!";
}
