<?php
// session_start();
$page = "sakums";
require "assets/header.php";
require "admin/database/con_db.php";

// Saglabātie sludinājumi
$saglabatie = [];
if (isset($_SESSION['lietotajaIdDt'])) {
    $lietotajsId = $_SESSION['lietotajaIdDt'];
    $stmt = $savienojums->prepare("
        SELECT id_sludinajums 
        FROM dzivote_saglabatie 
        WHERE id_lietotajs = ? AND sludinajuma_veids = 'Pirkt'
    ");
    $stmt->bind_param("i", $lietotajsId);
    $stmt->execute();
    $stmt->bind_result($savedId);
    while ($stmt->fetch()) {
        $saglabatie[] = $savedId;
    }
    $stmt->close();
}
?>

<section class="galvena">
    <div class="galvenaAttela">
        <img src="images/galvenaAttela.png">
    </div>
    <div class="galvenaisTeksts">
        <h1>Dzīvo, kur vēlies. <br>Sāciet ar mums!</h1>
    </div>
</section>

<section class="galvenaStatistika">
    <div class="kasteInfo">
        <h1>798</h1>
        <p>Mājokļi</p>
    </div>
    <img src="images/zieds.png">
    <div class="kasteInfo">
        <h1>25</h1>
        <p>Gadu pieredze</p>
    </div>
    <img src="images/zieds.png">
    <div class="kasteInfo">
        <h1>1500</h1>
        <p>Laimīgi klienti</p>
    </div>
    <img src="images/zieds.png">
    <div class="kasteInfo">
        <h1>120</h1>
        <p>Apbalvojumi</p>
    </div>
</section>

<section class="parMajaslapu">
    <div class="parInfo">
        <div class="kreisaKaste">
            <a href="parmums.php" class="btn">Par Dzīvo Te</a>
            <h1>Dzīvo Te — Jūsu mājas sākas šeit</h1>
        </div>
        <div class="labaKaste">
            <p>Nekustamā īpašuma izīrēšanas un pirkšanas platforma. Atrodiet perfektu māju vai dzīvokli, kurā dzīvot, īrēt vai ērti pārcelties. Ērta meklēšana, aktuāli saraksti un iespēja viegli pieteikt savu mājokli pārdošanai vai īrei. Ar mums Jūsu nekustamā īpašuma meklēšana kļūst vienkārša un ērta! Jūsu jaunās mājas sākas šeit – soli tuvāk sapņu dzīvesvietai.</p>
        </div>
    </div>
    <div class="attelas">
        <div class="pirmaAttela">
            <img src="images/parMumsAttela1.png">
        </div>
        <div class="otraAttela">
            <img src="images/parMumsAttela2.png">
        </div>
        <div class="tresaAttela">
            <img src="images/parMumsAttela3.png">
        </div>
    </div>
</section>

<section id="pedejiSludinajumi">
    <h1>Jaunpieejamās mājokļi pārdošanai</h1>
    <div class="lielaKaste">
        <?php
        $pedejiSludinajumiSQL = "
            SELECT majuvieta_pirkt.*, majuvieta_atteli.pirma_attela AS attela, 
                   majuvieta_adrese.* 
            FROM majuvieta_pirkt 
            INNER JOIN majuvieta_atteli ON majuvieta_pirkt.id_atteli = majuvieta_atteli.attelu_kopums_id 
            INNER JOIN majuvieta_adrese ON majuvieta_pirkt.id_adrese = majuvieta_adrese.adrese_id 
            ORDER BY izveidosanas_datums DESC LIMIT 4";

        $atlasaPedejiSludinajumi = mysqli_query($savienojums, $pedejiSludinajumiSQL);

        if (mysqli_num_rows($atlasaPedejiSludinajumi) > 0) {
            while ($sludinajums = mysqli_fetch_assoc($atlasaPedejiSludinajumi)) {
                $id = $sludinajums['pirkt_id'];
                $irSaglabats = in_array($id, $saglabatie);
                $sirdsKlase = $irSaglabats ? "fa-solid" : "fa-regular";
                $sirdsKlase2 = $irSaglabats ? "sirdsSarkans" : "";

                echo "
                <div class='sludinajums' data-id='{$id}'>
                    <div class='attela-sirds'>
                        <img src='data:image/jpeg;base64," . base64_encode($sludinajums['attela']) . "' />
                        <a class='sirds saglabaSludinajumu {$sirdsKlase2}' data-id='{$id}' data-veids='Pirkt'>
                            <i class='{$sirdsKlase} fa-heart'></i>
                        </a>
                    </div>
                    <p id='cena'>{$sludinajums['cena']} €</p>
                    <div id='papildInfo'>
                        <p><i class='fa-solid fa-door-open'></i> {$sludinajums['istabas']}</p>
                        <p><i class='fa-solid fa-ruler-combined'></i> {$sludinajums['platiba']} m<sup>2</sup></p>
                        <p><i class='fa-solid fa-stairs'></i> {$sludinajums['stavs_vai_stavi']}</p>
                    </div>
                    <p id='adrese'>{$sludinajums['pilseta']}, {$sludinajums['iela']} {$sludinajums['majas_numurs']}</p>
                </div>
                ";
            }
        } else {
            echo "<p>Tagad nav pieejami sludinājumi</p>";
        }
        ?>
    </div>
</section>

<section class="piedavajumi">
    <h1>Skatiet, kā Dzīvo Te var palīdzēt</h1>
    <div class="lielaKaste">
        <div class="mazaKaste">
            <img src="images/pirkt.png">
            <h3>Pirkt mājokli</h3>
            <p>Mājaslapā ir pieejami vairāk nekā 1 miljons+ pārdodamo māju, tāpēc Dzīvo Te var Jums atrast māju, kuru jūs vēlēsieties saukt par savām mājām.</p>
            <a href="pirkt.php" class="btn">Mājās</a>
        </div>
        <div class="mazaKaste">
            <img src="images/iret.png">
            <h3>Īrēt mājokli</h3>
            <p>Izmantojot vairāk nekā 35 filtrus un pielāgotu atslēgvārdu meklēšanu, Dzīvo Te var palīdzēt Jums viegli atrast māju vai dzīvokli īrei, kas Jums patiks.</p>
            <a href="iret.php" class="btn">Dzīvokli</a>
        </div>
        <div class="mazaKaste">
            <img src="images/pardot.png">
            <h3>Pievienot savu sludinājumu</h3>
            <p id="tresaisTeksts">Jūs varat ātri un ērti piedāvāt savu mājokli pārdošanai vai īrei. Aizpildiet vienkāršu veidlapu, pievienojiet fotogrāfijas un svarīgu informāciju, un Jūsu sludinājums būs pieejams tūkstošiem lietotāju.</p>
            <a href="pievienot.php" class="btn">Pievienot sludinājumu</a>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let saglabasanaNotiek = false;

        document.querySelectorAll(".sludinajums").forEach(function(card) {
            card.addEventListener("click", function() {
                const id = this.dataset.id;
                window.location.href = "maja_pirkt.php?id=" + id;
            });
        });

        document.querySelectorAll(".saglabaSludinajumu").forEach(function(sirds) {
            sirds.addEventListener("click", function(e) {
                e.stopPropagation();
                e.preventDefault();

                if (saglabasanaNotiek) return;
                saglabasanaNotiek = true;

                const id = this.dataset.id;
                const veids = this.dataset.veids;
                const irSaglabats = this.querySelector("i").classList.contains("fa-solid");

                const url = irSaglabats ?
                    "assets/database/dzest_saglabatu.php" :
                    "assets/database/pievienot_saglabatiem.php";

                const ikona = this.querySelector("i");
                const pats = this;

                fetch(url, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: `id_sludinajums=${id}&veids=${veids}`,
                    })
                    .then((res) => res.json())
                    .then((data) => {
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
                                window.location.href = "login.php";
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
        });
    });
</script>

<?php require "assets/footer.php"; ?>