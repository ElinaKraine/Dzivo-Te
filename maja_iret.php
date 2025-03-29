<?php
session_start();
$page = "majas";
require "assets/header.php";
require "admin/database/con_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $maja_id = intval($_POST['id']);
    $no = $_POST['no'];
    $lidz = $_POST['lidz'];
    $total = $_POST['total'];
    $datumsNo = new DateTime($no);
    $datumsLidz = new DateTime($lidz);
    $dienas = $datumsNo->diff($datumsLidz)->days + 1;
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
                <h2><?php echo $total; ?> € par <?php echo $dienas; ?> dienam</h2>
                <?php if (!isset($_SESSION['lietotajaLomaMV'])) { ?>
                    <a class="btn" href="login.php">Iznomāt</a>
                <?php } else { ?>
                    <a class="btn" data-target="#modal-ticket">Iznomāt</a>
                <?php } ?>
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
                    <p><i class="fa-solid fa-house"></i> <?php echo $sludinajums['iela'] . " " . $sludinajums['majas_numurs']; ?></p>
                    <p><i class="fas fa-calendar"></i> <?php echo $formattedNo; ?> - <?php echo $formattedLidz; ?></p>
                    <p><i class="fa-solid fa-money-bill-wave"></i> <?php echo $total; ?> € par <?php echo $dienas; ?> dienam</p>
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