<div class="filtriKaste iresanasfiltriKaste">
    <form class="filtri">
        <div class="meklesana">
            <input type="text" placeholder="Meklēt pilsētu, ielu" id="meklet-lauks" name="meklet-lauks">
        </div>
        <div class="kaste">
            <button type="button" name="cenaPoga" class="filter-poga">Cena <i class="fa-solid fa-chevron-down"></i></button>
            <div class="cenuDiapozons">
                <p>Cenu diapazons</p>
                <div class="cdiapozons">
                    <input type="number" name="minimalaCena" min="1" placeholder="No">
                    <p>-</p>
                    <input type="number" name="maksimalaCena" min="1" placeholder="Līdz">
                </div>
            </div>
        </div>
        <div class="kaste">
            <button type="button" name="istabasPoga" class="filter-poga">Istabas <i class="fa-solid fa-chevron-down"></i></button>
            <div class="istabasDiapozons">
                <p>Istabu skaits</p>
                <div class="idiapozons">
                    <input type="number" name="minimumIstabas" min="1" placeholder="No">
                    <p>-</p>
                    <input type="number" name="maksimumIstabas" min="1" placeholder="Līdz">
                </div>
            </div>
        </div>
        <div class="kaste">
            <button type="button" name="platibaPoga" class="filter-poga">Platība <i class="fa-solid fa-chevron-down"></i></button>
            <div class="platibasDiapozons">
                <p>Platība diapazons</p>
                <div class="pdiapozons">
                    <input type="number" name="minimalaPlatiba" min="1" placeholder="No">
                    <p>-</p>
                    <input type="number" name="maksimalaPlatiba" min="1" placeholder="Līdz">
                </div>
            </div>
        </div>
        <div class="kaste">
            <button type="button" name="staviPoga" class="filter-poga">Stāvi <i class="fa-solid fa-chevron-down"></i></button>
            <div class="staviDiapozons">
                <p>Stāvus skaits</p>
                <div class="pdiapozons">
                    <input type="number" name="minimumStavus" min="1" placeholder="No">
                    <p>-</p>
                    <input type="number" name="maksimumStavus" min="1" placeholder="Līdz">
                </div>
            </div>
        </div>
        <button class="mekleteFiltrusI mekleteFiltrus" type="submit" name="mekletI">Meklēt <i class="fas fa-search"></i></button>
        <a class="btn izdest-filtrus" id="izdest-filtrus-majas-iret">Izdzēst filtrus <i class="fa-solid fa-trash-can"></i></a>
    </form>
</div>
</div>

<div class="iresanasLapa">
    <div class="kreisaPuse">
        <h2>Mājas īrešanai</h2>
        <form class="kartosana">
            <select name="kartosanasOpcijasI" id="kartosanasOpcijasI">
                <option value="cena_desc">Kārtot: Cena(Aug - Zem)</option>
                <option value="cena_asc">Kārtot: Cena(Zem - Aug)</option>
                <option value="datums_desc" selected>Kārtot: Publicēšanas datums(Jauns - Vecs)</option>
                <option value="datums_asc">Kārtot: Publicēšanas datums(Vecs - Jauns)</option>
                <option value="platiba_desc">Kārtot: Platība(Aug - Zem)</option>
                <option value="platiba_asc">Kārtot: Platība(Zem - Aug)</option>
            </select>
        </form>
    </div>
    <div class="labaPuse">
        <form class="iresanasDatumi">
            <div class="datumi">
                <input id="registresanasDatumss" name="no" type="date"
                    placeholder="Reģistrēšanās datums"
                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                <p>-</p>
                <input id="izrakstisanasDatumss" name="lidz" type="date"
                    placeholder="Izrakstīšanās datums"
                    min="<?php echo date('Y-m-d', strtotime('+2 day')); ?>" required>
            </div>
            <button type="submit" class="btn" name="atlasitDatumus">Atlasiet</button>
        </form>
    </div>
</div>
<div class="majasSaturs iresanasBack">
    <div class="lielaKreisaPuse">
        <div class="sludinajumasKartinas" id="majasIret">

        </div>
    </div>
</div>