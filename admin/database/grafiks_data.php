<?php
require 'con_db.php';

$dates = [];
$data = [];

for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $dates[] = $day;

    // Pieteikumi
    $sql1 = "SELECT COUNT(*) FROM majuvieta_pieteikumi WHERE DATE(izveidosanas_datums) = ?";
    $stmt1 = $savienojums->prepare($sql1);
    $stmt1->bind_param("s", $day);
    $stmt1->execute();
    $stmt1->bind_result($pieteikumi);
    $stmt1->fetch();
    $stmt1->close();

    // Rezervācijas
    $sql2 = "SELECT COUNT(*) FROM majuvieta_iziresana WHERE DATE(izveidosanas_datums) = ?";
    $stmt2 = $savienojums->prepare($sql2);
    $stmt2->bind_param("s", $day);
    $stmt2->execute();
    $stmt2->bind_result($rezervacijas);
    $stmt2->fetch();
    $stmt2->close();

    // Sludinājumi (pirkt + iret)
    $sql3 = "
        SELECT (
            (SELECT COUNT(*) FROM majuvieta_pirkt WHERE DATE(izveidosanas_datums) = ?) +
            (SELECT COUNT(*) FROM majuvieta_iret WHERE DATE(izveidosanas_datums) = ?)
        ) AS skaits";
    $stmt3 = $savienojums->prepare($sql3);
    $stmt3->bind_param("ss", $day, $day);
    $stmt3->execute();
    $stmt3->bind_result($sludinajumi);
    $stmt3->fetch();
    $stmt3->close();

    $data[] = [
        'date' => $day,
        'pieteikumi' => $pieteikumi,
        'rezervacijas' => $rezervacijas,
        'sludinajumi' => $sludinajumi,
    ];
}

echo json_encode($data);
$savienojums->close();
