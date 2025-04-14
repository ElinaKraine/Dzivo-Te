<?php
session_start();
$page = "dzivokli";
require "assets/header.php";
require "admin/database/con_db.php";

if (isset($_GET['id'])) {
    $dzivoklis_id = intval($_GET['id']);
    $tips = "Dzīvoklis";

    $stmt = $savienojums->prepare("SELECT * FROM majuvieta_pirkt 
                                    INNER JOIN majuvieta_atteli ma ON majuvieta_pirkt.id_atteli = ma.attelu_kopums_id 
                                    INNER JOIN majuvieta_adrese md ON majuvieta_pirkt.id_adrese = md.adrese_id
                                    INNER JOIN majuvieta_lietotaji ml ON majuvieta_pirkt.id_ipasnieks = ml.lietotaja_id 
                                    WHERE pirkt_id = ? AND majokla_tips = ?");

    if (!$stmt) {
        die("Database query failed: " . mysqli_error($savienojums));
    }

    $stmt->bind_param("is", $dzivoklis_id, $tips);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($sludinajums = $result->fetch_assoc()) {
        $isSaved = false;

        if (isset($_SESSION['lietotajaIdDt'])) {
            $lietotajsId = $_SESSION['lietotajaIdDt'];
            $stmtSaglabats = $savienojums->prepare("
                SELECT 1 FROM dzivote_saglabatie 
                WHERE id_lietotajs = ? AND id_sludinajums = ? AND sludinajuma_veids = 'Pirkt'
            ");
            $stmtSaglabats->bind_param("ii", $lietotajsId, $dzivoklis_id);
            $stmtSaglabats->execute();
            $stmtSaglabats->store_result();
            $isSaved = $stmtSaglabats->num_rows > 0;
            $stmtSaglabats->close();
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
                    <a class='sirds <?php echo $isSaved ? "sirdsSarkans" : ""; ?>' data-id="<?php echo $sludinajums['pirkt_id']; ?>" data-veids="Pirkt">
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
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let saglabasanaNotiek = false;

                const sirdsPoga = document.querySelector(".sirds");

                if (sirdsPoga) {
                    sirdsPoga.addEventListener("click", function(e) {
                        e.preventDefault();
                        if (saglabasanaNotiek) return;
                        saglabasanaNotiek = true;

                        const sludinajumaId = this.dataset.id;
                        const veids = this.dataset.veids;
                        const irSaglabats = this.querySelector("i").classList.contains("fa-solid");

                        const url = irSaglabats ?
                            "./assets/database/dzest_saglabatu.php" :
                            "./assets/database/pievienot_saglabatiem.php";

                        const ikona = this.querySelector("i");
                        const pats = this;

                        fetch(url, {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded",
                                },
                                body: `id_sludinajums=${sludinajumaId}&veids=${veids}`,
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    if (irSaglabats) {
                                        ikona.classList.remove("fa-solid");
                                        ikona.classList.add("fa-regular");
                                        pats.classList.remove("sirdsSarkans");
                                    } else {
                                        ikona.classList.remove("fa-regular");
                                        ikona.classList.add("fa-solid");
                                        pats.classList.add("sirdsSarkans");
                                    }
                                } else {
                                    if (data.message === "unauthorized") {
                                        window.location.href = "./login.php";
                                    } else {
                                        alert(data.message || "Darbība neizdevās.");
                                    }
                                }
                            })
                            .catch(() => {
                                alert("Neizdevās veikt darbību.");
                            })
                            .finally(() => {
                                saglabasanaNotiek = false;
                            });
                    });
                }
            });
        </script>

<?php
    } else {
        echo "<p class='neveiksmigsPazinojums'>Dzīvoklis nav atrasts</p>";
    }
    $stmt->close();
} else {
    echo "<p class='neveiksmigsPazinojums'>Kļūda: ID nav norādīts</p>";
}

require "assets/footer.php";
?>