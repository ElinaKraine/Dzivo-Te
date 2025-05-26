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

<div class="modal modalSludinajums" id="modal-admin-sludinajums">
    <div class="modal-box">
        <div class="virsraksts">
            <h2>Sludinājums</h2>
            <div class="close-modal"><i class="fas fa-times"></i></div>
        </div>
        <form id="sludinajumaForma">
            <div class="formElements">
                <div class="rinda">
                    <label>Mājokļa tips:</label>
                    <select id="majoklaTips" name="majoklaTips" required>
                        <option value="maja">Māja</option>
                        <option value="dzivoklis">Dzīvoklis</option>
                    </select>
                    <p id="majoklaTips-text"></p>
                </div>
                <div class="rinda">
                    <label>Darījuma veids:</label>
                    <select id="majoklaVeids" name="majoklaVeids" required>
                        <option value="pirkt">Pirkt</option>
                        <option value="iret">Īrēt</option>
                    </select>
                </div>
                <div class="rinda">
                    <label>Pilsēta:</label>
                    <input type="text" id="pilseta" name="pilseta" required>
                </div>
                <div class="rinda">
                    <label>Iela:</label>
                    <input type="text" id="iela" name="iela" required>
                </div>
                <div class="rinda">
                    <label>Mājas numurs:</label>
                    <input type="text" id="majasNumurs" name="majasNumurs" required>
                </div>
                <div class="rinda" id="dzivokla-numurs">
                    <label>Dzīvokļa numurs:</label>
                    <input type="text" id="dzivoklaNumurs" name="dzivoklaNumurs">
                </div>
                <div class="rinda" id="pirkt-cena">
                    <label>Cena (€):</label>
                    <input type="number" id="cenaPirkt" name="cenaPirkt" min="1">
                </div>
                <div class="rinda iret-cena">
                    <label>€/dienā:</label>
                    <input type="number" id="cenaDiena" name="cenaDiena" min="1">
                </div>
                <div class="rinda iret-cena">
                    <label>€/nedēļā:</label>
                    <input type="number" id="cenaNedela" name="cenaNedela" min="1">
                </div>
                <div class="rinda iret-cena">
                    <label>€/mēnesī:</label>
                    <input type="number" id="cenaMenesi" name="cenaMenesi" min="1">
                </div>
                <div class="rinda">
                    <label>Platība (m<sup>2</sup>):</label>
                    <input type="number" id="platiba" name="platiba" min="1" required>
                </div>
                <div class="rinda" id="zemes-platiba">
                    <label>Zemes platība (m<sup>2</sup>):</label>
                    <input type="number" id="zemesPlatiba" name="zemesPlatiba" min="1">
                </div>
                <div class="rinda">
                    <label>Istabas:</label>
                    <input type="number" id="istabas" name="istabas" min="1">
                </div>
                <div class="rinda" id="maja-stavi">
                    <label>Stāvi:</label>
                    <input type="number" id="stavi" name="stavi" min="1">
                </div>
                <div class="rinda" id="dziv-stavs">
                    <label>Stāvs:</label>
                    <input type="text" id="stavs" name="stavs">
                </div>
                <div class="rinda">
                    <label>Apraksts:</label>
                    <textarea id="apraksts" name="apraksts" rows="5"></textarea>
                </div>
                <div class="rinda" id="atteluGalerijaContainerAdmin" style="display: none;">
                    <label>Attēli:</label>
                    <div id="atteluGalerijaAdmin"></div>
                </div>
                <div class="rinda nomainitAttelusRinda">
                    <label>Nomainīt attēlus?</label>
                    <select id="sludNomainitAtteliSelect" name="nomainitAtteli">
                        <option value="ne">Nē</option>
                        <option value="ja">Jā</option>
                    </select>
                </div>
                <div class="rinda nomainit-slud-atteli">
                    <label>Attēli:</label>
                    <input type="file" id="atteli" name="atteli[]" accept="image/png, image/jpeg" multiple>
                </div>
                <div class="rinda">
                    <label>Statuss:</label>
                    <select id="sludNomainitStatusu" name="sludNomainitStatusu">
                        <option value="Iesniegts sludinājums">Iesniegts sludinājums</option>
                        <option value="Sludinājuma pārskatīšana">Sludinājuma pārskatīšana</option>
                        <option value="Apsiprināts | Publicēts">Apsiprināts | Publicēts</option>
                        <option value="Atteikums">Atteikums</option>
                    </select>
                </div>
                <input type="hidden" id="slud_ID" name="slud_ID">
            </div>
            <button type="submit" name="sludinajums_saglabat" id="sludinajums_saglabat" class="btn">Saglabāt</button>
        </form>
    </div>
</div>

<?php if (isset($_SESSION['pazinojumsMVL'])): ?>
    <div class="modal modal-active" id="modal-message">
        <div class="modal-box">
            <div class="close-modal" data-target="#modal-message"><i class="fas fa-times"></i></div>
            <h2>
                <?php
                echo $_SESSION['pazinojumsMVL'];
                unset($_SESSION['pazinojumsMVL']);
                ?>
            </h2>
        </div>
    </div>
<?php endif; ?>

</div>
</body>

</html>