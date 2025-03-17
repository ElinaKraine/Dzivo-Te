<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nosutit"])) {
    require "admin/database/con_db.php";

    $epasts = $_SESSION['lietotajsMV'];
    // $id_majuvieta_pirkt = ;
    $ip_adrese = $_SERVER['REMOTE_ADDR'];

    // if(!empty($epasts)){
    //     $vaicajums = $savienojums->prepare("INSERT INTO majuvieta_pieteikumi(epasts, id_majuvieta_pirkt, statuss, ip_adrese) VALUES (?, ?, ?, ?)");
    //     $vaicajums->bind_param("siss", $epasts, $id_majuvieta_pirkt, default, $ip_adrese);
    //     if($vaicajums->execute()){
    //         $_SESSION['pazinojumsMV'] = "Pieteikums veiksmīgi nosutīts!";
    //     }else{
    //         $_SESSION['pazinojumsMV'] = "Kļūda sistemā.";
    //     }
    //     $vaicajums->close();
    //     $savienojums->close();
    // }else{
    //     $_SESSION['pazinojumsMV'] = "Kaut kas bija nepareizi!";
    // }
}

header("Location: ./");
