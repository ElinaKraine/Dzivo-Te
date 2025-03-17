    <div class="filtriKaste">
        <form class="filtri">
            <div class="meklesana">
                <input type="text" placeholder="Meklēt pilsētu" id="meklet-lauks" name="meklet-lauks">
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
            <button class="mekleteFiltrus" type="submit">Meklēt <i class="fas fa-search"></i></button>
            <a class="btn" id="izdest-filtrus">Izdzēst filtrus <i class="fa-solid fa-trash-can"></i></a>
        </form>
    </div>
    </div>

    <div class="iresanasLapa">
        <div class="kreisaPuse">
            <h2>Mājas īrešanai</h2>
            <form class="kartosana">
                <select name="" id="">
                    <option value="">Kārtot: Cena(Aug - Zem)</option>
                    <option value="">Kārtot: Cena(Zem - Aug)</option>
                    <option value="" selected>Kārtot: Publicēšanas datums(Jauns - Vecs)</option>
                    <option value="">Kārtot: Publicēšanas datums(Vecs - Jauns)</option>
                    <option value="">Kārtot: Platība(Aug - Zem)</option>
                    <option value="">Kārtot: Platība(Zem - Aug)</option>
                </select>
            </form>
        </div>
        <div class="labaPuse">
            <form class="iresanasDatumi">
                <div class="datumi">
                    <i class="fa fa-calendar"></i>
                    <input name="no" placeholder="Registresanas datums" onfocus="(this.type='date')" onblur="(this.type='text')" required>
                    <p>-</p>
                    <input name="lidz" placeholder="Izrakstisanas datums" onfocus="(this.type='date')" onblur="(this.type='text')" required>
                </div>
                <button type="submit" class="btn">Atlasiet</button>
            </form>
        </div>
    </div>
    <div class="majasSaturs iresanasBack">
        <div class="lielaKreisaPuse">
            <div class="sludinajumasKartinas">

            </div>
        </div>
        <div class="karte">

        </div>
    </div>