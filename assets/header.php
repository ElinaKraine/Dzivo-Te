<?php
    session_start();

    $header_file = 'header_neAutorL.php';

    if (isset($_SESSION['lietotajaLomaMV'])) {
        if ($_SESSION['lietotajaLomaMV'] == 'Lietotājs') {
            $header_file = 'header_autorL.php';
        } else {
            $header_file = 'header_neAutorL.php';
        }
    }

    require $header_file;
?>