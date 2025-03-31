<?php
session_start();
$page = "majas";
require "assets/header.php";
require "admin/database/con_db.php";

if (isset($_GET['id'])) {
    $maja_id = intval($_GET['id']);

    $stmt = $savienojums->prepare("SELECT * FROM majuvieta_iret 
                                    INNER JOIN majuvieta_atteli ma ON majuvieta_iret.id_atteli = ma.attelu_kopums_id 
                                    INNER JOIN majuvieta_adrese md ON majuvieta_iret.id_adrese = md.adrese_id
                                    INNER JOIN majuvieta_lietotaji ml ON majuvieta_iret.id_ipasnieks = ml.lietotaja_id 
                                    WHERE iret_id = ?");

    if (!$stmt) {
        die("Database query failed: " . mysqli_error($savienojums));
    }

    $stmt->bind_param("i", $maja_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($sludinajums = $result->fetch_assoc()) {
        $isSaved = false;

        if (isset($_SESSION['lietotajaIdDt'])) {
            $lietotajsId = $_SESSION['lietotajaIdDt'];
            $stmtSaglabats = $savienojums->prepare("
                SELECT 1 FROM dzivote_saglabatie 
                WHERE id_lietotajs = ? AND id_sludinajums = ? AND sludinajuma_veids = 'Iret'
            ");
            $stmtSaglabats->bind_param("ii", $lietotajsId, $maja_id);
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
                    <a class='sirds <?php echo $isSaved ? "sirdsSarkans" : ""; ?>' data-id="<?php echo $sludinajums['iret_id']; ?>" data-veids="Iret">
                        <i class='<?php echo $isSaved ? "fa-solid" : "fa-regular"; ?> fa-heart'></i>
                    </a>
                <?php } ?>
            </div>
            <div class="pamatInfo">
                <h2><?php echo $sludinajums['iela'] . " " . $sludinajums['majas_numurs']; ?></h2>
            </div>
            <div class="papildInfo">
                <p>Latvija, <?php echo $sludinajums['pilseta'] . " " . $sludinajums['pasts_indekss']; ?></p>
                <div class="ikoninasArInfo">
                    <p><i class='fa-solid fa-door-open'></i> <?php echo $sludinajums['istabas']; ?></p>
                    <p><i class='fa-solid fa-ruler-combined'></i> <?php echo $sludinajums['platiba']; ?> m<sup>2</sup></p>
                    <p><i class='fa-solid fa-stairs'></i> <?php echo $sludinajums['stavi_vai_stavs']; ?></p>
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
        echo "<p>Māja nav atrasta</p>";
    }
    $stmt->close();
} else {
    echo "<p>Kļūda: ID nav norādīts</p>";
}

require "assets/footer.php";
?>