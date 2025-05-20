<?php
require 'con_db.php';

if (isset($_POST['ielogoties'])) {
    session_start();

    $epasts = htmlspecialchars($_POST['epasts']);
    $parole = $_POST['parole'];

    if (!empty($epasts) && !empty($parole)) {
        $vaicajums = $savienojums->prepare("SELECT * FROM majuvieta_lietotaji WHERE epasts = ? AND statuss != 'Dzēsts'");
        $vaicajums->bind_param("s", $epasts);
        $vaicajums->execute();
        $rezultats = $vaicajums->get_result();
        $lietotajs = $rezultats->fetch_assoc();

        if ($lietotajs && password_verify($parole, $lietotajs["parole"])) {
            $_SESSION['lietotajsMV'] = $lietotajs["epasts"];
            $_SESSION['lietotajaLomaMV'] = $lietotajs["loma"];
            $_SESSION['lietotajaIdDt'] = $lietotajs["lietotaja_id"];

            if ($_SESSION['lietotajaLomaMV'] == 'Lietotājs') {
                header("location: ../../index.php");
            } elseif (in_array($_SESSION['lietotajaLomaMV'], ['Administrators', 'Moderators'])) {
                header("location: ../index.php");
            } else {
                $_SESSION['pazinojumsMV'] = "Lietotājs neidentificēts";
                header("location: ../../login.php");
            }
        } else {
            $_SESSION['pazinojumsMV'] = "Nepareizs lietotajvārds vai parole";
            header("location: ../../login.php");
        }

        $vaicajums->close();
        $savienojums->close();
    } else {
        $_SESSION['pazinojumsMV'] = "Jāaizpilda visi obligātie lauki! *";
        header("location: ../../login.php");
    }
}
