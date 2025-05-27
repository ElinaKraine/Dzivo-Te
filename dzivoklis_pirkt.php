<?php
$page = "dzivokli";
require "assets/header.php";
require "admin/database/con_db.php";

if (isset($_GET['id'])) {
    $dzivoklis_id = intval($_GET['id']);
    $tips = "Dzīvoklis";
    $veids = "Pirkt";
    $statuss = "Apsiprināts | Publicēts";

    $vaicajums = $savienojums->prepare(
        "SELECT * FROM majuvieta_pirkt 
        INNER JOIN majuvieta_adrese md ON majuvieta_pirkt.pirkt_id = md.id_sludinajums
        INNER JOIN majuvieta_atteli ma ON majuvieta_pirkt.pirkt_id = ma.id_sludinajums 
        INNER JOIN majuvieta_lietotaji ml ON majuvieta_pirkt.id_ipasnieks = ml.lietotaja_id 
        WHERE pirkt_id = ?
        AND majokla_tips = ?
        AND md.sludinajuma_veids = ?
        AND ma.sludinajuma_veids = ?
        AND majuvieta_pirkt.statuss = ?"
    );
    $vaicajums->bind_param("issss", $dzivoklis_id, $tips, $veids, $veids, $statuss);
    $vaicajums->execute();
    $rezultats = $vaicajums->get_result();
    $vaicajums->close();

    if ($sludinajums = $rezultats->fetch_assoc()) {
        $isSaved = false;

        if (isset($_SESSION['lietotajaIdDt'])) {
            $lietotajsId = $_SESSION['lietotajaIdDt'];
            $vaicajums = $savienojums->prepare("SELECT 1 FROM dzivote_saglabatie 
                                                    WHERE id_lietotajs = ? AND id_sludinajums = ?
                                                    AND sludinajuma_veids = 'Pirkt'");
            $vaicajums->bind_param("ii", $lietotajsId, $dzivoklis_id);
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
                    <a class='sirds <?php echo $isSaved ? "sirdsSarkans" : ""; ?>' data-id="<?php echo $sludinajums['pirkt_id']; ?>" data-veids="Pirkt" data-tips="Dzivoklis">
                        <i class='<?php echo $isSaved ? "fa-solid" : "fa-regular"; ?> fa-heart'></i>
                    </a>
                <?php } ?>
            </div>
            <div class="pamatInfo">
                <h2><?php echo $sludinajums['iela'] . " " . $sludinajums['majas_numurs'] . "/" . $sludinajums['dzivokla_numurs']; ?></h2>
                <h2><?php echo $sludinajums['cena']; ?> €</h2>
                <?php if (!isset($_SESSION['lietotajaLomaMV'])) { ?>
                    <a class="btn" href="login.php">Pieteikties iegādei</a>
                <?php } else { ?>
                    <a class="btn" data-target="#modal-ticket">Pieteikties iegādei</a>
                <?php } ?>
            </div>
            <div class="papildInfo">
                <p>Latvija, <?php echo $sludinajums['pilseta']; ?></p>
                <div class="ikoninasArInfo">
                    <p><i class='fa-solid fa-door-open'></i> <?php echo $sludinajums['istabas']; ?></p>
                    <p><i class='fa-solid fa-ruler-combined'></i> <?php echo $sludinajums['platiba']; ?> m<sup>2</sup></p>
                    <p><i class='fa-solid fa-stairs'></i> <?php echo $sludinajums['stavs_vai_stavi']; ?></p>
                </div>
            </div>
            <div class="apraksts">
                <p><?php echo $sludinajums['apraksts']; ?></p>
            </div>
            <?php if (isset($_SESSION['lietotajaLomaMV'])) { ?>
                <div class="ipasnieks">
                    <h3>Jautājumi par sludinājumu?</h3>
                    <p><i class="fa-solid fa-envelope"></i> <?php echo $sludinajums['epasts']; ?></p>
                </div>
            <?php } ?>
        </section>

        <?php if (isset($_SESSION['pazinojumsMV'])): ?>
            <div class="modal modal-active" id="modal-message">
                <div class="modal-box">
                    <div class="close-modal" data-target="#modal-message"><i class="fas fa-times"></i></div>
                    <h2>
                        <?php
                        echo $_SESSION['pazinojumsMV'];
                        unset($_SESSION['pazinojumsMV']);
                        ?>
                    </h2>
                </div>
            </div>
        <?php endif; ?>

        <div class="modal" id="modal-ticket">
            <div class="modal-box">
                <div class="close-modal" data-target="#modal-ticket"><i class="fas fa-times"></i></div>
                <h2>Pieteikties iegādei</h2>
                <div class="pamatInfo">
                    <p><i class="fa-solid fa-house"></i> <?php echo $sludinajums['iela'] . " " . $sludinajums['majas_numurs']; ?></p>
                    <p><i class="fa-solid fa-money-bill-wave"></i> <?php echo $sludinajums['cena']; ?> €</p>
                    <p><i class="fa-solid fa-user"></i> <?php echo $_SESSION['lietotajsMV']; ?></p>
                </div>
                <form action="pieteikumi.php" method="POST">
                    <input type="hidden" name="id_majuvieta_pirkt" value="<?php echo $sludinajums['pirkt_id']; ?>">
                    <button type="submit" name="nosutit" class="btn">Nosūtīt pieteikumu</button>
                </form>
            </div>
        </div>
<?php
    } else {
        echo "<p class='neveiksmigsPazinojums'>Dzīvoklis nav atrasts</p>";
    }
} else {
    echo "<p class='neveiksmigsPazinojums'>Kļūda: ID nav norādīts</p>";
}

require "assets/footer.php";
?>