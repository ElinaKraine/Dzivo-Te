<?php
require '../../admin/database/con_db.php';
if (isset($_POST['id'])) {
    $id = htmlspecialchars($_POST['id']);

    $vaicajums = $savienojums->prepare("SELECT * FROM majuvieta_lietotaji WHERE statuss != 'Dzēsts' AND lietotaja_id = ?");
    $vaicajums->bind_param('i', $id);
    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();
    if (!$rezultats) {
        die('Kļūda: ' . $savienojums->error);
    }

    while ($ieraksts = $rezultats->fetch_assoc()) {
        $json[] = array(
            'id' => htmlspecialchars($ieraksts['lietotaja_id']),
            'epasts' => htmlspecialchars($ieraksts['epasts']),
            'vards' => htmlspecialchars($ieraksts['vards']),
            'uzvards' => htmlspecialchars($ieraksts['uzvards']),
            'talrunis' => htmlspecialchars($ieraksts['talrunis']),
            'attels' => base64_encode($ieraksts['attels']),
            'ip_adrese' => htmlspecialchars($ieraksts['ip_adrese']),
            'atjauninasanas_datums' => date("d.m.Y H:i", strtotime($ieraksts['atjauninasanas_datums'])),
            'loma' => htmlspecialchars($ieraksts['loma']),
        );
    }

    $vaicajums->close();
    $savienojums->close();

    $jsonstring = json_encode($json[0]);
    echo $jsonstring;
}
