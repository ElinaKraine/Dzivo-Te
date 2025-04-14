<?php
session_start();
$page = "dzivokli";
require "assets/header.php";
require "admin/database/con_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $maja_id = intval($_POST['id']);
    $no = $_POST['no'];
    $lidz = $_POST['lidz'];
    $total = $_POST['total'];
    $datumsNo = new DateTime($no);
    $datumsLidz = new DateTime($lidz);
    $dienas = $datumsNo->diff($datumsLidz)->days;
    $formattedNo = $datumsNo->format('d.m.Y');
    $formattedLidz = $datumsLidz->format('d.m.Y');

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

        function pareizaDienasForma($skaits)
        {
            if ($skaits === 1 || ($skaits % 10 === 1 && $skaits % 100 !== 11)) {
                return "dienu";
            }
            return "dienām";
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
                <h2><?php echo $sludinajums['iela'] . " " . $sludinajums['majas_numurs'] . "/" . $sludinajums['dzivokla_numurs']; ?></h2>
                <h2><?php echo $total; ?> € par <?php echo $dienas . " " . pareizaDienasForma($dienas); ?></h2>
                <?php if (!isset($_SESSION['lietotajaLomaMV'])) { ?>
                    <a class="btn" href="login.php">Iznomāt</a>
                <?php } else { ?>
                    <a class="btn" data-target="#modal-ticket">Iznomāt</a>
                <?php } ?>
            </div>
            <div class="papildInfo">
                <p>Latvija, <?php echo $sludinajums['pilseta']; ?></p>
                <div class="ikoninasArInfo">
                    <p><i class='fa-solid fa-door-open'></i> <?php echo $sludinajums['istabas']; ?></p>
                    <p><i class='fa-solid fa-ruler-combined'></i> <?php echo $sludinajums['platiba']; ?> m<sup>2</sup></p>
                    <p><i class='fa-solid fa-stairs'></i> <?php echo $sludinajums['stavi_vai_stavs']; ?></p>
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
                <h2>Iznomāšana</h2>
                <div class="pamatInfo">
                    <p><i class="fa-solid fa-house"></i> <?php echo $sludinajums['iela'] . " " . $sludinajums['majas_numurs'] . "/" . $sludinajums['dzivokla_numurs']; ?></p>
                    <p><i class="fas fa-calendar"></i> <?php echo $formattedNo; ?> - <?php echo $formattedLidz; ?></p>
                    <p><i class="fa-solid fa-money-bill-wave"></i> <?php echo $total; ?> € par <?php echo $dienas . " " . pareizaDienasForma($dienas); ?></p>
                    <p><i class="fa-solid fa-user"></i> <?php echo $_SESSION['lietotajsMV']; ?></p>
                </div>
                <form action="iziresana.php" method="POST">
                    <input type="hidden" name="id_majuvieta_iret" value="<?php echo $sludinajums['iret_id']; ?>">
                    <input type="hidden" name="cena" value="<?php echo $total; ?>">
                    <input type="hidden" name="registresanasDatums" value="<?php echo $datumsNo->format('Y-m-d'); ?>">
                    <input type="hidden" name="izrakstisanasDatums" value="<?php echo $datumsLidz->format('Y-m-d'); ?>">
                    <button type="submit" name="apstiprinat" class="btn">Apstiprināt</button>
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
        echo "<p class='neveiksmigsPazinojums'>Māja nav atrasta</p>";
    }
    $stmt->close();
} else {
    echo "<p class='neveiksmigsPazinojums'>Kļūda: ID nav norādīts</p>";
}

require "assets/footer.php";
?>