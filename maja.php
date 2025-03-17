<?php
session_start();
$page = "majas";
require "assets/header.php";
require "admin/database/con_db.php";

if (isset($_GET['id'])) {
    $maja_id = intval($_GET['id']);

    $stmt = $savienojums->prepare("SELECT * FROM majuvieta_pirkt 
                                    INNER JOIN majuvieta_atteli ma ON majuvieta_pirkt.id_atteli = ma.attelu_kopums_id 
                                    INNER JOIN majuvieta_adrese md ON majuvieta_pirkt.id_adrese = md.adrese_id 
                                    WHERE pirkt_id = ?");

    if (!$stmt) {
        die("Database query failed: " . mysqli_error($savienojums));
    }

    $stmt->bind_param("i", $maja_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($sludinajums = $result->fetch_assoc()) {
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
                    <a class='sirds'><i class='fa-regular fa-heart'></i></a>
                <?php } ?>
            </div>
            <div class="pamatInfo">
                <h2><?php echo $sludinajums['iela'] . " " . $sludinajums['majas_numurs']; ?></h2>
                <h2><?php echo $sludinajums['cena']; ?> €</h2>
                <?php if (!isset($_SESSION['lietotajaLomaMV'])) { ?>
                    <a class="btn" href="login.php">Pieteikties iegādei</a>
                <?php } else { ?>
                    <a class="btn" data-target="#modal-ticket">Pieteikties iegādei</a>
                <?php } ?>
            </div>
            <div class="papildInfo">
                <p>Latvija, <?php echo $sludinajums['pilseta'] . " " . $sludinajums['pasts_indekss']; ?></p>
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
            <?php if (isset($_SESSION['lietotajaLomaMV'])) { ?>
                <div class="ipasnieks">
                    <h3>Jautājumi par sludinājumu?</h3>
                    <p><i class="fa-solid fa-envelope"></i> <?php echo $sludinajums['epasts']; ?></p>
                </div>
            <?php } ?>
        </section>

        <div class="modal" id="modal-ticket">
            <div class="modal-box">
                <div class="close-modal" data-target="#modal-ticket"><i class="fas fa-times"></i></div>
                <h2 data-i18n="izveidot_jaunu_piet">Pieteikties iegādei</h2>
                <div class="pamatInfo">
                    <p><i class="fa-solid fa-house"></i> <?php echo $sludinajums['iela'] . " " . $sludinajums['majas_numurs']; ?></p>
                    <p><i class="fa-solid fa-money-bill-wave"></i> <?php echo $sludinajums['cena']; ?> €</p>
                    <p><i class="fa-solid fa-user"></i> <?php echo $_SESSION['lietotajsMV']; ?></p>
                </div>
                <form action="pieteikumi.php" method="POST">
                    <input type="hidden" id="<?php echo $sludinajums['pirkt_id']; ?>">
                    <button data-i18n="nosutit_piet" type="submit" name="nosutit" class="btn">Nosūtīt pieteikumu</button>
                </form>
            </div>
        </div>

<?php
    } else {
        echo "<p>Māja nav atrasta</p>";
    }
    $stmt->close();
} else {
    echo "<p>Kļūda: ID nav norādīts</p>";
}

require "assets/footer.php";
?>