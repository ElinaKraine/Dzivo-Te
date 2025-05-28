<?php
$page = "sakums";
require "assets/header.php";
require "admin/database/con_db.php";

// Saglabātie sludinājumi
$saglabatie = [];
if (isset($_SESSION['lietotajaIdDt'])) {
    $lietotajsId = $_SESSION['lietotajaIdDt'];
    $vaicajums = $savienojums->prepare("SELECT id_sludinajums 
                                        FROM dzivote_saglabatie 
                                        WHERE id_lietotajs = ? AND sludinajuma_veids = 'Pirkt'");
    $vaicajums->bind_param("i", $lietotajsId);
    $vaicajums->execute();
    $vaicajums->bind_result($savedId);
    while ($vaicajums->fetch()) {
        $saglabatie[] = $savedId;
    }
    $vaicajums->close();
}

$vaicajums = "SELECT (SELECT COUNT(*) FROM majuvieta_pirkt) + (SELECT COUNT(*) FROM majuvieta_iret) AS sludinajumu_skaits";
$sludinajumuSkaits = $savienojums->query($vaicajums)->fetch_row()[0];
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
    <div class="statistikaDiv">
        <div class="kasteInfo">
            <h1><?php echo $sludinajumuSkaits; ?></h1>
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
    </div>
    <div class="kasteInfoMobile">
        <div class="kolonna">
            <div class="kasteInfo">
                <h1><?php echo $sludinajumuSkaits; ?></h1>
                <p>Mājokļi</p>
            </div>
            <div class="kasteInfo">
                <h1>1500</h1>
                <p>Laimīgi klienti</p>
            </div>
        </div>
        <div class="kolonna">
            <img src="images/zieds.png">
            <img src="images/zieds.png">
        </div>
        <div class="kolonna">
            <div class="kasteInfo">
                <h1>25</h1>
                <p>Gadu pieredze</p>
            </div>
            <div class="kasteInfo">
                <h1>120</h1>
                <p>Apbalvojumi</p>
            </div>
        </div>
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
            <img src="images/parMumsAttela1.jpg">
        </div>
        <div class="otraAttela">
            <img src="images/parMumsAttela2.jpg">
        </div>
        <div class="tresaAttela">
            <img src="images/parMumsAttela3.jpg">
        </div>
    </div>
</section>

<section id="pedejiSludinajumi">
    <h1>Jaunpieejamās mājokļi pārdošanai</h1>
    <div class="lielaKaste">
        <?php
        $pedejiSludinajumiSQL =
            "SELECT majuvieta_pirkt.*, majuvieta_adrese.*,
                    majuvieta_atteli.pirma_attela AS attela
            FROM majuvieta_pirkt 
            INNER JOIN majuvieta_atteli ON majuvieta_pirkt.pirkt_id = majuvieta_atteli.id_sludinajums 
            INNER JOIN majuvieta_adrese ON majuvieta_pirkt.pirkt_id = majuvieta_adrese.id_sludinajums 
            WHERE majuvieta_atteli.sludinajuma_veids = 'Pirkt'
            AND majuvieta_adrese.sludinajuma_veids = 'Pirkt'
            AND majuvieta_pirkt.statuss = 'Apsiprināts | Publicēts'
            ORDER BY izveidosanas_datums DESC 
            LIMIT 4";
        $atlasaPedejiSludinajumi = mysqli_query($savienojums, $pedejiSludinajumiSQL);

        if (mysqli_num_rows($atlasaPedejiSludinajumi) > 0) {
            while ($sludinajums = mysqli_fetch_assoc($atlasaPedejiSludinajumi)) {
                $id = $sludinajums['pirkt_id'];
                $irSaglabats = in_array($id, $saglabatie);
                $sirdsKlase = $irSaglabats ? "fa-solid" : "fa-regular";
                $sirdsKlase2 = $irSaglabats ? "sirdsSarkans" : "";
                $tips = "";
                switch ($sludinajums['majokla_tips']) {
                    case 'Mājas':
                        $tips = "Maja";
                        break;
                    case 'Dzīvoklis':
                        $tips = "Dzivoklis";
                        break;
                }

                echo "
                <div class='sludinajums sludinajumsIndex' data-id='{$id}' data-tips='{$tips}'>
                    <div class='attela-sirds'>
                        <img src='data:image/jpeg;base64," . base64_encode($sludinajums['attela']) . "' />
                        <a class='sirds saglabaSludinajumu {$sirdsKlase2}' data-id='{$id}' data-veids='Pirkt' data-tips='{$tips}'>
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
            <p>Mājaslapā ir pieejami daudzi mājokļi pārdošanai, tāpēc Dzīvo Te var Jums atrast māju, kuru jūs vēlēsieties saukt par savām mājām.</p>
            <a href="majas.php" class="btn">Mājas</a>
        </div>
        <div class="mazaKaste">
            <img src="images/iret.png">
            <h3>Īrēt mājokli</h3>
            <p>Izmantojot filtrus un pielāgotu atslēgvārdu meklēšanu, Dzīvo Te var palīdzēt Jums viegli atrast māju vai dzīvokli īrei, kas Jums patiks.</p>
            <a href="dzivokli.php" class="btn">Dzīvokļi</a>
        </div>
        <div class="mazaKaste">
            <img src="images/pardot.png">
            <h3>Pievienot savu sludinājumu</h3>
            <p id="tresaisTeksts">Jūs varat ātri un ērti piedāvāt savu mājokli pārdošanai vai īrei. Aizpildiet vienkāršu veidlapu, pievienojiet fotogrāfijas un svarīgu informāciju, un Jūsu sludinājums būs pieejams tūkstošiem lietotāju.</p>
            <?php if (isset($_SESSION['lietotajaIdDt'])): ?>
                <a href="profils.php" class="btn">Pievienot sludinājumu</a>
            <?php else: ?>
                <a href="login.php" class="btn">Pievienot sludinājumu</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require "assets/footer.php"; ?>