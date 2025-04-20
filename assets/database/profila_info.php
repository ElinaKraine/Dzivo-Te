<?php
require '../../admin/database/con_db.php';
session_start();

if (isset($_SESSION['lietotajaIdDt'])) {
    $lietotajaId = $_SESSION['lietotajaIdDt'];

    $stmt = $savienojums->prepare('SELECT * FROM majuvieta_lietotaji WHERE izdzests = 0 AND lietotaja_id = ?');
    $stmt->bind_param('i', $lietotajaId);

    if ($stmt->execute()) {
        $rezultats = $stmt->get_result();
        $json = array();

        while ($ieraksts = $rezultats->fetch_assoc()) {
            $json[] = array(
                'id' => htmlspecialchars($ieraksts['lietotaja_id']),
                'vards' => htmlspecialchars($ieraksts['vards']),
                'uzvards' => htmlspecialchars($ieraksts['uzvards']),
                'epasts' => htmlspecialchars($ieraksts['epasts']),
                'talrunis' => htmlspecialchars($ieraksts['talrunis']),
                'attels' => base64_encode($ieraksts['attels'])
            );
        }

        $jsonstring = json_encode($json);
        echo $jsonstring;
    } else {
        echo json_encode(array('error' => 'Query failed: ' . $stmt->error));
    }

    $stmt->close();
}

$savienojums->close();
