<?php
$page = "sludinajumi";
require "assets/header.php";
require "database/con_db.php";
?>
<div class="sludinajumi">
    <div class="pievienotKaste">
        <a class="pievienotBtn" id="new-btn-slud"><i class="fas fa-add"></i> Pievienot sludinājumu</a>
    </div>
    <table>
        <tr class="heading">
            <th>ID</th>
            <th>Mājokļa tips</th>
            <th>Veids</th>
            <th>Īpašnieks</th>
            <th>Adrese</th>
            <th>Cena (€)</th>
            <th>Platība (m<sup>2</sup>)</th>
            <th>Izveidošanas datums</th>
            <th>Statuss</th>
            <th></th>
        </tr>
        <tbody id="sludinajumi-saraksts"></tbody>
    </table>
    <div id="pagination" class="pagination-container"></div>
</div>

<div class="modal modalSludinajums" id="modal-admin-admin-sludinajums">
    <div class="modal-box">
        <div class="virsraksts">
            <h2>Sludinājums</h2>
            <div class="close-modal"><i class="fas fa-times"></i></div>
        </div>
        <form id="sludinajumaFormaAdmin">
            <div class="formElements">
                <div class="rinda">
                    <label>Mājokļa tips: &nbsp;<span class="sarkans">*</span></label>
                    <select id="majoklaTipsAdmin" name="majoklaTipsAdmin" required>
                        <option value="maja">Māja</option>
                        <option value="dzivoklis">Dzīvoklis</option>
                    </select>
                    <p id="majoklaTips-text-admin"></p>
                </div>
                <div class="rinda">
                    <label>Darījuma veids: &nbsp;<span class="sarkans">*</span></label>
                    <select id="majoklaVeidsAdmin" name="majoklaVeidsAdmin" required>
                        <option value="pirkt">Pirkt</option>
                        <option value="iret">Īrēt</option>
                    </select>
                </div>
                <div class="rinda">
                    <label>Pilsēta: &nbsp;<span class="sarkans">*</span></label>
                    <input type="text" id="pilsetaAdmin" name="pilsetaAdmin" required>
                </div>
                <div class="rinda">
                    <label>Iela: &nbsp;<span class="sarkans">*</span></label>
                    <input type="text" id="ielaAdmin" name="ielaAdmin" required>
                </div>
                <div class="rinda">
                    <label>Mājas numurs: &nbsp;<span class="sarkans">*</span></label>
                    <input type="text" id="majasNumursAdmin" name="majasNumursAdmin" required>
                </div>
                <div class="rinda" id="dzivokla-numurs-admin">
                    <label>Dzīvokļa numurs: &nbsp;<span class="sarkans">*</span></label>
                    <input type="text" id="dzivoklaNumursAdmin" name="dzivoklaNumursAdmin">
                </div>
                <div class="rinda" id="pirkt-cena-admin">
                    <label>Cena (€): &nbsp;<span class="sarkans">*</span></label>
                    <input type="number" id="cenaPirktAdmin" name="cenaPirktAdmin" min="1">
                </div>
                <div class="rinda iret-cena">
                    <label>€/dienā: &nbsp;<span class="sarkans">*</span></label>
                    <input type="number" id="cenaDienaAdmin" name="cenaDienaAdmin" min="1">
                </div>
                <div class="rinda iret-cena">
                    <label>€/nedēļā: &nbsp;<span class="sarkans">*</span></label>
                    <input type="number" id="cenaNedelaAdmin" name="cenaNedelaAdmin" min="1">
                </div>
                <div class="rinda iret-cena">
                    <label>€/mēnesī: &nbsp;<span class="sarkans">*</span></label>
                    <input type="number" id="cenaMenesiAdmin" name="cenaMenesiAdmin" min="1">
                </div>
                <div class="rinda">
                    <label>Platība (m<sup>2</sup>): &nbsp;<span class="sarkans">*</span></label>
                    <input type="number" id="platibaAdmin" name="platibaAdmin" min="1" required>
                </div>
                <div class="rinda" id="zemes-platiba-admin">
                    <label>Zemes platība (m<sup>2</sup>): &nbsp;<span class="sarkans">*</span></label>
                    <input type="number" id="zemesPlatibaAdmin" name="zemesPlatibaAdmin" min="1">
                </div>
                <div class="rinda">
                    <label>Istabas: &nbsp;<span class="sarkans">*</span></label>
                    <input type="number" id="istabasAdmin" name="istabasAdmin" min="1">
                </div>
                <div class="rinda" id="maja-stavi-admin">
                    <label>Stāvi: &nbsp;<span class="sarkans">*</span></label>
                    <input type="number" id="staviAdmin" name="staviAdmin" min="1">
                </div>
                <div class="rinda" id="dziv-stavs-admin">
                    <label>Stāvs: &nbsp;<span class="sarkans">*</span></label>
                    <input type="text" id="stavsAdmin" name="stavsAdmin">
                </div>
                <div class="rinda">
                    <label>Apraksts:</label>
                    <textarea id="aprakstsAdmin" name="aprakstsAdmin" rows="5"></textarea>
                </div>
                <div class="rinda" id="atteluGalerijaContainer" style="display: none;">
                    <label>Attēli:</label>
                    <div id="atteluGalerija"></div>
                </div>
                <div class="rinda nomainitAttelusRinda">
                    <label>Nomainīt attēlus?</label>
                    <select id="sludNomainitAtteliSelectAdmin" name="nomainitAtteli">
                        <option value="ne">Nē</option>
                        <option value="ja">Jā</option>
                    </select>
                </div>
                <div class="rinda nomainit-slud-atteli">
                    <label>Attēli: &nbsp;<span class="sarkans">*</span></label>
                    <input type="file" id="atteliAdmin" name="atteliAdmin[]" accept="image/png, image/jpeg" multiple>
                </div>
                <div class="rinda">
                    <label>Statuss: &nbsp;<span class="sarkans">*</span></label>
                    <select id="sludNomainitStatusuAdmin" name="sludNomainitStatusuAdmin">
                        <option value="Iesniegts sludinājums">Iesniegts sludinājums</option>
                        <option value="Sludinājuma pārskatīšana">Sludinājuma pārskatīšana</option>
                        <option value="Apsiprināts | Publicēts">Apsiprināts | Publicēts</option>
                        <option value="Atteikums">Atteikums</option>
                    </select>
                </div>
                <div class="rinda papildInfoSlud">
                    <label>Atjaunināšanas datums:</label>
                    <p id="atjauninasanasDatumsSlud"></p>
                </div>
                <div class="rinda papildInfoSlud">
                    <label>IP adrese:</label>
                    <p name="ipAdreseSlud" id="ipAdreseSlud"></p>
                </div>
                <input type="hidden" id="slud_ID" name="slud_ID">
                <div id="sludFormPazinojumsAdmin" class="formPazinojums"></div>
            </div>
            <button type="submit" name="sludinajums_saglabat_admin" id="sludinajums_saglabat_admin" class="btn">Saglabāt</button>
        </form>
    </div>
</div>

<div id="imageModal" class="modal">
    <div class="modal-atteli"><span class="close-modal">&times;</span>
        <span id="prevImage">&#10094;</span>
        <img id="modalImage" />
        <span id="nextImage">&#10095;</span>
    </div>
</div>

</div>
</body>

</html>