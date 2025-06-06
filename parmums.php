<?php
$page = "parmums";
require "assets/header.php";
require "admin/database/con_db.php";

$vaicajums = "SELECT (SELECT COUNT(*) FROM majuvieta_pirkt) + (SELECT COUNT(*) FROM majuvieta_iret) AS sludinajumu_skaits";
$sludinajumuSkaits = $savienojums->query($vaicajums)->fetch_row()[0];
?>

<section class="parMums">
    <div class="parMumsTeksts">
        <h1>Par mums</h1>
        <p>Dzīvo Te ir ērta platforma mājokļu meklēšanai Latvijā. Mēs piedāvājam plašu māju un dzīvokļu klāstu īrei vai iegādei. Vienkārša navigācija, aktuālie piedāvājumi un noderīgi filtri palīdzēs ātri atrast ideālo mājokli ērtai dzīvošanai vai investīcijām.</p>
    </div>
    <div class="attelas">
        <div class="parMums1">
            <img src="images/parMums1.jpg">
        </div>
        <div class="parMums2">
            <img src="images/parMums2.jpg">
        </div>
        <div class="parMums3">
            <img src="images/parMums3.png">
        </div>
    </div>
</section>

<section class="komentariji">
    <div class="komentarijs">
        <img src="images/komentars1.png">
        <div class="saturs">
            <i class="fa-solid fa-quote-left"></i>
            <div class="teksts">
                <p>Mūsu mērķis bija piedāvāt risinājumu, kas būtu piemērots dažādām dzīves situācijām, un es lepojos, ka tas mums ir izdevies.</p>
                <p class="komentarijaIpasnieks">Roberta Henrija <span>Dzīvo Te direktors</span></p>
            </div>
        </div>
    </div>
    <div class="komentarijs">
        <img src="images/komentars2.png">
        <div class="saturs">
            <i class="fa-solid fa-quote-left"></i>
            <div class="teksts">
                <p>Es lepojos ar filtrēšanas sistēmu un to, ka mūsu platforma nodrošina vienkāršu un efektīvu lietotāju pieredzi. Kā tehniskais direktors es īpaši pievērsu uzmanību drošībai un stabilitātei.</p>
                <p class="komentarijaIpasnieks">Anna Kozlova <span>Tehniskais direktors</span></p>
            </div>
        </div>
    </div>
</section>

<section class="lietotajaDarbibas">
    <h1>Ko lietotājs var darīt mājaslapā <span>Dzīvo Te</span>?</h1>
    <div class="darbibas">
        <div class="darbiba">
            <h2><i class="fa-solid fa-lock-open"></i> Autorizēti lietotāji var</h2>
            <ul>
                <li>Pieteikties mājokļa iegādei</li>
                <li>Izīrēt mājokli</li>
                <li>Publicēt sludinājumus par mājokļa pārdošanu vai izīrēšanu</li>
                <li>Saglabāt sludinājumus personīgajā sarakstā</li>
                <li>Sekot līdzi savām darbībām</li>
            </ul>
        </div>
        <div class="darbiba">
            <h2><i class="fa-solid fa-lock"></i> Neautorizēti lietotāji var</h2>
            <ul>
                <li>Pārlūkot mājokļu sludinājumus</li>
                <li>Apskatīt informāciju par mājokļa īpašībām un atrašanās vietu kartē</li>
                <li>Izmantot meklēšanu un filtrus, lai atrastu mājokļus</li>
            </ul>
        </div>
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

<section id="sazinaties">
    <div class="zina">
        <h2>Sazinieties ar mums!</h2>
        <p>Mēs ticam sadarbībai un novērtējam jūsu ieguldījumu visā procesā. Mēs aicinām klientus aktīvi piedalīties diskusijās, dalīties ar savām idejām, vēlmēm un atsauksmēm.</p>
        <form action="" method="post">
            <label>Vārds Uzvārds <span>*</span></label>
            <input type="text" name="vardsUzvards" required>
            <label>E-pasta adrese <span>*</span></label>
            <input type="email" name="zinaEpasts" required>
            <label>Tālrunis <span>*</span></label>
            <input type="text" name="zinaTalrunis" required>
            <label>Ziņa <span>*</span></label>
            <textarea name="zina" cols="30" rows="10" required></textarea>
            <button type="submit" name="nosutit" class="btn">Sazināties</button>
            <?php
            require "assets/mail.php";
            ?>
        </form>
    </div>
    <div class="karte">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2201.533222695734!2d21.0121609290902!3d56.510247252771585!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46faa6176ecda02b%3A0xc9b579799a66ccb5!2zTGllbMSBIGllbGEgMTAsIExpZXDEgWphLCBMVi0zNDAx!5e0!3m2!1sru!2slv!4v1737532031928!5m2!1sru!2slv" width="100%" height="100%" style="border:0; border-radius:1rem;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>

<?php
require "assets/footer.php";
?>