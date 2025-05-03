<?php
require '../../admin/database/con_db.php';

if (isset($_POST['id']) && isset($_POST['veids'])) {
    $id = intval($_POST['id']);
    $veids = $_POST['veids'] === 'Pirkt' ? 'majuvieta_pirkt' : 'majuvieta_iret';
    $veids_id = $veids === 'majuvieta_pirkt' ? 'pirkt_id' : 'iret_id';

    $sql = "SELECT * FROM $veids WHERE $veids_id = ?";
    $stmt = $savienojums->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $rez = $stmt->get_result();
    $dati = $rez->fetch_assoc();

    $sql2 = "SELECT * FROM majuvieta_adrese WHERE sludinajuma_veids = ? AND id_sludinajums = ?";
    $stmt2 = $savienojums->prepare($sql2);
    $stmt2->bind_param("si", $_POST['veids'], $id);
    $stmt2->execute();
    $rez2 = $stmt2->get_result();
    $adrese = $rez2->fetch_assoc();

    $json = [
        "majokla_tips" => $dati['majokla_tips'],
        "platiba" => $dati['platiba'],
        "istabas" => $dati['istabas'],
        "apraksts" => $dati['apraksts'],
        "pilseta" => $adrese['pilseta'],
        "iela" => $adrese['iela'],
        "majas_numurs" => $adrese['majas_numurs'],
        "dzivokla_numurs" => $adrese['dzivokla_numurs'] ?? null,
        "zemes_platiba" => $dati['zemes_platiba'] ?? null,
        "stavi" => $dati['majokla_tips'] === 'Mājas' ? $dati['stavs_vai_stavi'] : null,
        "stavs" => $dati['majokla_tips'] === 'Dzīvoklis' ? $dati['stavs_vai_stavi'] : null,
        "cena" => $dati['cena'] ?? null,
        "cena_diena" => $dati['cena_diena'] ?? null,
        "cena_nedela" => $dati['cena_nedela'] ?? null,
        "cena_menesis" => $dati['cena_menesis'] ?? null
    ];

    $atteluSql = "SELECT * FROM majuvieta_atteli WHERE sludinajuma_veids = ? AND id_sludinajums = ?";
    $stmt3 = $savienojums->prepare($atteluSql);
    $stmt3->bind_param("si", $_POST['veids'], $id);
    $stmt3->execute();
    $atteluRez = $stmt3->get_result();
    $atteli = $atteluRez->fetch_assoc();

    $atteluBazes = [];
    foreach (['pirma_attela', 'otra_attela', 'tresa_attela', 'ceturta_attela', 'piektaa_attela', 'sesta_attela', 'septita_attela', 'astota_attela', 'devita_attela', 'desmita_attela'] as $key) {
        if (!empty($atteli[$key])) {
            $atteluBazes[] = base64_encode($atteli[$key]);
        }
    }

    $json["atteli"] = $atteluBazes;

    echo json_encode($json);
}
