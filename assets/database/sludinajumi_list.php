<?php
require '../../admin/database/con_db.php';
session_start();

if (isset($_SESSION['lietotajaIdDt'])) {
    $lietotajaId = $_SESSION['lietotajaIdDt'];
    $json = [];

    // Pirkt sludinājumi
    $query = "
                SELECT 
                    'Pirkt' AS veids, 
                    'majuvieta_pirkt' AS tabula, 
                    mv.pirkt_id AS id, 
                    mv.majokla_tips, 
                    mv.cena, 
                    mv.platiba,
                    mv.statuss, 
                    mv.izveidosanas_datums,
                    ad.pilseta, 
                    ad.iela, 
                    ad.majas_numurs
                FROM majuvieta_pirkt mv
                JOIN majuvieta_adrese ad ON mv.id_adrese = ad.adrese_id
                WHERE mv.id_ipasnieks = ?
            ";
    $stmt = $savienojums->prepare($query);
    $stmt->bind_param('i', $lietotajaId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $json[] = [
            'id' => htmlspecialchars($row['id']),
            'veids' => $row['veids'],
            'tabula' => $row['tabula'],
            'majokla_tips' => htmlspecialchars($row['majokla_tips']),
            'cena' => htmlspecialchars($row['cena']),
            'platiba' => htmlspecialchars($row['platiba']),
            'statuss' => htmlspecialchars($row['statuss']),
            'izveidosanas_datums' => date("d.m.Y", strtotime($row['izveidosanas_datums'])),
            'pilseta' => htmlspecialchars($row['pilseta']),
            'iela' => htmlspecialchars($row['iela']),
            'majas_numurs' => htmlspecialchars($row['majas_numurs']),
        ];
    }
    $stmt->close();

    // Iret sludinājumi
    $query = "
                SELECT 
                    'Iret' AS veids, 
                    'majuvieta_iret' AS tabula, 
                    mv.iret_id AS id, 
                    mv.majokla_tips, 
                    mv.cena_menesis AS cena, 
                    mv.platiba,
                    mv.statuss, 
                    mv.izveidosanas_datums,
                    ad.pilseta, 
                    ad.iela, 
                    ad.majas_numurs
                FROM majuvieta_iret mv
                JOIN majuvieta_adrese ad ON mv.id_adrese = ad.adrese_id
                WHERE mv.id_ipasnieks = ?
            ";
    $stmt = $savienojums->prepare($query);
    $stmt->bind_param('i', $lietotajaId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $json[] = [
            'id' => htmlspecialchars($row['id']),
            'veids' => $row['veids'],
            'tabula' => $row['tabula'],
            'majokla_tips' => htmlspecialchars($row['majokla_tips']),
            'cena' => htmlspecialchars($row['cena']),
            'platiba' => htmlspecialchars($row['platiba']),
            'statuss' => htmlspecialchars($row['statuss']),
            'izveidosanas_datums' => date("d.m.Y", strtotime($row['izveidosanas_datums'])),
            'pilseta' => htmlspecialchars($row['pilseta']),
            'iela' => htmlspecialchars($row['iela']),
            'majas_numurs' => htmlspecialchars($row['majas_numurs']),
        ];
    }
    $stmt->close();
}

$savienojums->close();

echo json_encode($json);
