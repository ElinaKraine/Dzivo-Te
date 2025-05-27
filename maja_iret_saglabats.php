<?php
session_start();
$page = "majas";
require "assets/header.php";
require "admin/database/con_db.php";

if (isset($_GET['id'])) {
    $maja_id = intval($_GET['id']);
    $tips = "Mājas";
    $veids = "Iret";
    $statuss = "Apsiprināts | Publicēts";

    $vaicajums = $savienojums->prepare(
        "SELECT * FROM majuvieta_iret mi
        INNER JOIN majuvieta_adrese md ON mi.iret_id = md.id_sludinajums
        INNER JOIN majuvieta_atteli ma ON mi.iret_id = ma.id_sludinajums
        INNER JOIN majuvieta_lietotaji ml ON mi.id_ipasnieks = ml.lietotaja_id
        WHERE mi.iret_id = ? AND mi.majokla_tips = ?
        AND md.sludinajuma_veids = ?
        AND ma.sludinajuma_veids = ?
        AND mi.statuss = ?"
    );
    $vaicajums->bind_param("issss", $maja_id, $tips, $veids, $veids, $statuss);
    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();
    $vaicajums->close();

    if ($sludinajums = $rezultats->fetch_assoc()) {
        $isSaved = false;

        if (isset($_SESSION['lietotajaIdDt'])) {
            $lietotajsId = $_SESSION['lietotajaIdDt'];
            $vaicajums = $savienojums->prepare(
                "SELECT 1 FROM dzivote_saglabatie 
                WHERE id_lietotajs = ? AND id_sludinajums = ?
                AND sludinajuma_veids = 'Iret' AND majokla_tips = 'Maja'
            "
            );
            $vaicajums->bind_param("ii", $lietotajsId, $maja_id);
            $vaicajums->execute();
            $vaicajums->store_result();
            $isSaved = $vaicajums->num_rows > 0;
            $vaicajums->close();
        }
?>

        <section class="galvena majaLapa">
            <div class="visasBildes attela-sirds">
                <div class="viensAttela">
                    <img src='data:image/jpeg;base64,<?php echo base64_encode($sludinajums['pirma_attela']); ?>' />
                </div>
                <div class="diviAtteliKopa">
                    <div class="diviAttela">
                        <img src='data:image/jpeg;base64,<?php echo base64_encode($sludinajums['otra_attela']); ?>' />
                    </div>
                    <div class="trisAttela">
                        <img src='data:image/jpeg;base64,<?php echo base64_encode($sludinajums['tresa_attela']); ?>' />
                    </div>
                </div>
                <?php if (!isset($_SESSION['lietotajaLomaMV'])) { ?>
                    <a href="login.php" class='sirds'><i class='fa-regular fa-heart'></i></a>
                <?php } else { ?>
                    <a class='sirds <?php echo $isSaved ? "sirdsSarkans" : ""; ?>' data-id="<?php echo $sludinajums['iret_id']; ?>" data-veids="Iret" data-tips="Maja">
                        <i class='<?php echo $isSaved ? "fa-solid" : "fa-regular"; ?> fa-heart'></i>
                    </a>
                <?php } ?>
            </div>
            <div class="pamatInfo">
                <h2><?php echo $sludinajums['iela'] . " " . $sludinajums['majas_numurs']; ?></h2>
            </div>
            <div class="papildInfo">
                <p>Latvija, <?php echo $sludinajums['pilseta']; ?></p>
                <div class="ikoninasArInfo">
                    <p><i class='fa-solid fa-door-open'></i> <?php echo $sludinajums['istabas']; ?></p>
                    <p><i class='fa-solid fa-ruler-combined'></i> <?php echo $sludinajums['platiba']; ?> m<sup>2</sup></p>
                    <p><i class='fa-solid fa-stairs'></i> <?php echo $sludinajums['stavs_vai_stavi']; ?></p>
                    <p><i class="fa-solid fa-chart-area"></i> <?php echo $sludinajums['zemes_platiba']; ?> m<sup>2</sup></p>
                </div>
            </div>
            <div class="apraksts">
                <p><?php echo $sludinajums['apraksts']; ?></p>
            </div>
            <div class="cenas">
                <h2><?php echo $sludinajums['cena_diena']; ?> €/dienā</h2>
                <h2><?php echo $sludinajums['cena_nedela']; ?> €/nedēļā</h2>
                <h2><?php echo $sludinajums['cena_menesis']; ?> €/mēnesī</h2>
            </div>
            <?php if (isset($_SESSION['lietotajaLomaMV'])) { ?>
                <div class="ipasnieks">
                    <h3>Jautājumi par sludinājumu?</h3>
                    <p><i class="fa-solid fa-envelope"></i> <?php echo $sludinajums['epasts']; ?></p>
                </div>
            <?php } ?>
        </section>
<?php
    } else {
        echo "<p class='neveiksmigsPazinojums'>Māja nav atrasta</p>";
    }
} else {
    echo "<p class='neveiksmigsPazinojums'>Kļūda: ID nav norādīts</p>";
}

require "assets/footer.php";
?>