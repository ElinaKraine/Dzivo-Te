    <div class="filtriKaste">
        <div class="meklesana">
            <input type="text" placeholder="Meklēt pilsētu vai adrese">
            <a name="meklet"><i class="fas fa-search"></i></a>
        </div>
        <form class="filtri">
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
            <button class="izdzestFiltrusPoga">Izdzēst filtrus</button>
        </form>
    </div>
</div>

<div class="majasSaturs">
    <div class="lielaKreisaPuse">
        <h2>Mājas pārdošanai</h2>
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
        <div class="sludinajumasKartinas">
            
        </div>
    </div>
    <div class="karte">

    </div>
</div>

