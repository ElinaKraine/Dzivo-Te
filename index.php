<?php
    $page = "sakums";
    require "assets/header.php";
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
            <h1>3000</h1>
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
                <a href="parmums.php" class="btn">Par Māju Vieta</a>
                <h1>Māju Vieta — Jūsu mājas sākas šeit</h1>
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
                require "admin/database/con_db.php";

                $pedejiSludinajumiSQL = "SELECT majuvieta_pirkt.*, majuvieta_atteli.pirma_attela AS attela, majuvieta_adrese.* FROM majuvieta_pirkt INNER JOIN majuvieta_atteli ON majuvieta_pirkt.id_atteli = majuvieta_atteli.attelu_kopums_id INNER JOIN majuvieta_adrese ON majuvieta_pirkt.id_adrese = majuvieta_adrese.adrese_id LIMIT 4";
                $atlasaPedejiSludinajumi = mysqli_query($savienojums, $pedejiSludinajumiSQL);

                if(mysqli_num_rows($atlasaPedejiSludinajumi) > 0){
                    while($sludinajums = mysqli_fetch_assoc($atlasaPedejiSludinajumi)){
                        echo "
                            <div class='sludinajums'>
                                <div class='attela-sirds'>
                                    <img src='data:image/jpeg;base64," . base64_encode($sludinajums['attela']) . "' />
                                    <a class='sirds'><i class='fa-regular fa-heart'></i></a>
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
                }else{
                    echo "Tagad nav pieejami sludinājumi";
                }
            ?>
        </div>
    </section>

    <section class="piedavajumi">
        <h1>Skatiet, kā Māju Vieta var palīdzēt</h1>
        <div class="lielaKaste">
            <div class="mazaKaste">
                <img src="images/pirkt.png">
                <h3>Pirkt mājokli</h3>
                <p>Mājaslapā ir pieejami vairāk nekā 1 miljons+ pārdodamo māju, tāpēc Māju Vieta var Jums atrast māju, kuru jūs vēlēsieties saukt par savām mājām.</p>
                <a href="" class="btn">Mājās</a>
            </div>
            <div class="mazaKaste">
                <img src="images/iret.png">
                <h3>Īrēt mājokli</h3>
                <p>Izmantojot vairāk nekā 35 filtrus un pielāgotu atslēgvārdu meklēšanu, Māju Vieta var palīdzēt Jums viegli atrast māju vai dzīvokli īrei, kas Jums patiks.</p>
                <a href="" class="btn">Dzīvokli</a>
            </div>
            <div class="mazaKaste">
                <img src="images/pardot.png">
                <h3>Pievienot savu sludinājumu</h3>
                <p id="tresaisTeksts">Jūs varat ātri un ērti piedāvāt savu mājokli pārdošanai vai īrei. Aizpildiet vienkāršu veidlapu, pievienojiet fotogrāfijas un svarīgu informāciju, un Jūsu sludinājums būs pieejams tūkstošiem lietotāju.</p>
                <a href="" class="btn">Pievienot sludinājumu</a>
            </div>
        </div>
    </section>

<?php
    require "assets/footer.php";
?>