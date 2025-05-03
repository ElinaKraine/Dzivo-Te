<?php
$page = "profils";
require "assets/header.php";

if (!isset($_SESSION['lietotajaLomaMV'])) {
    header("Location: index.php");
    exit();
}
?>

<div class="profilsKaste">
    <div class="fons">
    </div>
    <div class="melnsFons">
    </div>
    <div class="kaste">
        <div class="profilaInfo">
            <div id="profila_info">
            </div>
            <a class="btn" href="admin/database/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Izlogoties</a>
        </div>
        <div class="tabulas">
            <div class="tabulasSaraksts">
                <button class="tabulaPoga atlasitaTabula" data-tab="sludinajumi_list">Sludinājumi</button>
                <button class="tabulaPoga neAtlasitaTabula" data-tab="pieteikumi_list">Mani pieteikumi</button>
                <button class="tabulaPoga neAtlasitaTabula" data-tab="ire_list">Mana īre</button>
                <button class="tabulaPoga neAtlasitaTabula" data-tab="liet_pieteikumi_list">Lietotāju pieteikumi</button>
                <button class="tabulaPoga neAtlasitaTabula" data-tab="liet_ire_list">Lietotāju īre</button>
            </div>
            <div id="tabula">

            </div>
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
                        <div class="rinda" id="atteluGalerijaContainer" style="display: none;">
                            <label>Attēli:</label>
                            <div id="atteluGalerija"></div>
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
                        <input type="hidden" id="id_sludinajums" name="id_sludinajums">
                    </div>
                    <button type="submit" name="sludinajums_saglabat" id="sludinajums_saglabat" class="btn">Saglabāt</button>
                </form>
            </div>
        </div>

        <div class="modal modalStatuss" id="modal-admin-pieteikums">
            <div class="modal-box">
                <div class="virsraksts">
                    <h2>Pieteikums</h2>
                    <div class="close-modal"><i class="fas fa-times"></i></div>
                </div>
                <form id="pieteikumaForma">
                    <div class="formElements">
                        <label>Lietotājs:</label>
                        <input type="text" id="pietlietotajs" name="pietlietotajs" disabled>
                        <label>Mājokļa tips:</label>
                        <input type="text" id="pietMajoklaTips" name="pietMajoklaTips" disabled>
                        <label>Adrese:</label>
                        <input type="text" id="pietAdrese" name="pietAdrese" disabled>
                        <label>Cena (€):</label>
                        <input type="text" id="pietCena" name="pietCena" disabled>
                        <label>Datums:</label>
                        <input type="text" id="pietDatums" name="pietDatums" disabled>
                        <label>Statuss:</label>
                        <select id="pietStatuss" name="pietStatuss" required>
                            <option value="iesniegtsPieteikums">Iesniegts pieteikums</option>
                            <option value="pieteikumaParskatisana">Pieteikuma pārskatīšana</option>
                            <option value="majoklaIegadesProcesa">Mājokļa iegādes procesā</option>
                            <option value="majoklisIrIegadats">Mājoklis ir iegādāts</option>
                            <option value="atteikums">Atteikums</option>
                        </select>
                        <input type="hidden" id="pieteikums_ID">
                    </div>
                    <button type="submit" name="pieteikums_saglabat" id="pieteikums_saglabat" class="btn">Saglabāt</button>
                </form>
            </div>
        </div>

        <div class="modal modalLietotajs" id="modal-admin-lietotajs">
            <div class="modal-box">
                <div class="virsraksts">
                    <h2>Lietotāja informācija</h2>
                    <div class="close-modal"><i class="fas fa-times"></i></div>
                </div>
                <form id="lietotajaForma">
                    <div class="formElements">
                        <div class="rinda">
                            <label>Vārds:</label>
                            <input type="text" id="lietVards" name="lietVards" required>
                        </div>
                        <div class="rinda">
                            <label>Uzvārds:</label>
                            <input type="text" id="lietUzvards" name="lietUzvards" required>
                        </div>
                        <div class="rinda">
                            <label>Epasts:</label>
                            <input type="text" id="lietEpasts" name="lietEpasts" required>
                        </div>
                        <div class="rinda">
                            <label>Tālrunis:</label>
                            <input type="text" id="lietTalrunis" name="lietTalrunis" required>
                        </div>
                        <div class="rinda">
                            <label>Nomainīt parole?</label>
                            <select id="nomainitParoleSelect" name="nomainitParole">
                                <option value="ne">Nē</option>
                                <option value="ja">Jā</option>
                            </select>
                        </div>
                        <div class="rinda nomainitParole">
                            <label>Parole:</label>
                            <input type="password" id="lietParole" name="lietParole">
                        </div>
                        <div class="rinda nomainitParole">
                            <label>Parole (atkārtoti):</label>
                            <input type="password" id="lietParoleOtrais" name="lietParoleOtrais">
                        </div>
                        <div class="rinda">
                            <label>Nomainīt attēlu?</label>
                            <select id="nomainitAtteluSelect" name="nomainitAttelu">
                                <option value="ne">Nē</option>
                                <option value="ja">Jā</option>
                            </select>
                        </div>
                        <div class="rinda" id="nomainitAttelu">
                            <label>Attēls:</label>
                            <input type="file" id="attels" name="attels" accept="image/png, image/jpeg">
                        </div>
                        <div class="rinda">
                            <input type="hidden" id="liet_ID" name="liet_ID">
                        </div>
                    </div>
                    <button type="submit" name="lietotajs_saglabat" id="lietotajs_saglabat" class="btn">Saglabāt</button>
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
    </div>
</div>

<?php
require "assets/footer.php";
?>