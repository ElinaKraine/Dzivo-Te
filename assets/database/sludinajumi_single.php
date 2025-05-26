<?php
require '../../admin/database/con_db.php';

if (isset($_POST['id']) && isset($_POST['veids'])) {
    $id = intval($_POST['id']);
    $veids = $_POST['veids'] === 'Pirkt' ? 'majuvieta_pirkt' : 'majuvieta_iret';
    $veids_id = $veids === 'majuvieta_pirkt' ? 'pirkt_id' : 'iret_id';

    $sqlTeikums = "SELECT * FROM $veids WHERE $veids_id = ?";
    $vaicajums = $savienojums->prepare($sqlTeikums);
    $vaicajums->bind_param("i", $id);
    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();
    $dati = $rezultats->fetch_assoc();
    $vaicajums->close();

    $sqlTeikums = "SELECT * FROM majuvieta_adrese WHERE sludinajuma_veids = ? AND id_sludinajums = ?";
    $vaicajums = $savienojums->prepare($sqlTeikums);
    $vaicajums->bind_param("si", $_POST['veids'], $id);
    $vaicajums->execute();
    $rezultats2 = $vaicajums->get_result();
    $adrese = $rezultats2->fetch_assoc();
    $vaicajums->close();

    $json = [
        "majokla_tips" => htmlspecialchars($dati['majokla_tips']),
        "platiba" => htmlspecialchars($dati['platiba']),
        "istabas" => htmlspecialchars($dati['istabas']),
        "apraksts" => htmlspecialchars($dati['apraksts']),
        "pilseta" => htmlspecialchars($adrese['pilseta']),
        "iela" => htmlspecialchars($adrese['iela']),
        "majas_numurs" => htmlspecialchars($adrese['majas_numurs']),
        "dzivokla_numurs" => htmlspecialchars($adrese['dzivokla_numurs']) ?? null,
        "zemes_platiba" => htmlspecialchars($dati['zemes_platiba']) ?? null,
        "stavi" => htmlspecialchars($dati['majokla_tips']) === 'Mājas' ? $dati['stavs_vai_stavi'] : null,
        "stavs" => htmlspecialchars($dati['majokla_tips']) === 'Dzīvoklis' ? $dati['stavs_vai_stavi'] : null,
        "cena" => htmlspecialchars($dati['cena']) ?? null,
        "cena_diena" => htmlspecialchars($dati['cena_diena']) ?? null,
        "cena_nedela" => htmlspecialchars($dati['cena_nedela']) ?? null,
        "cena_menesis" => htmlspecialchars($dati['cena_menesis']) ?? null,
        "statuss" => htmlspecialchars($dati['statuss']),
        'ip_adrese' => htmlspecialchars($dati['ip_adrese']),
        'atjauninasanas_datums' => date("d.m.Y H:i", strtotime($dati['atjauninasanas_datums'])),
    ];

    $atteluSql = "SELECT * FROM majuvieta_atteli WHERE sludinajuma_veids = ? AND id_sludinajums = ?";
    $vaicajums = $savienojums->prepare($atteluSql);
    $vaicajums->bind_param("si", $_POST['veids'], $id);
    $vaicajums->execute();
    $atteluRezultats = $vaicajums->get_result();
    $atteli = $atteluRezultats->fetch_assoc();
    $vaicajums->close();

    $atteluBazes = [];
    foreach (['pirma_attela', 'otra_attela', 'tresa_attela', 'ceturta_attela', 'piektaa_attela', 'sesta_attela', 'septita_attela', 'astota_attela', 'devita_attela', 'desmita_attela'] as $key) {
        if (!empty($atteli[$key])) {
            $atteluBazes[] = base64_encode($atteli[$key]);
        }
    }

    $json["atteli"] = $atteluBazes;

    echo json_encode($json);
}
$savienojums->close();
