<?php
$page = "lietotaji";
require "assets/header.php";
require "database/con_db.php";
?>
<div class="lietotaji">
    <div class="pievienotKaste">
        <a class="pievienotBtn" id="new-btn-lietotajs"><i class="fas fa-add"></i> Pievienot lietotāju</a>
    </div>
    <table>
        <tr class="heading">
            <th>ID</th>
            <th>Vārds Uzvārds</th>
            <th>E-pasts</th>
            <th>Tālrunis</th>
            <th>Loma</th>
            <th>Izveidošanas datums</th>
            <th></th>
        </tr>
        <tbody id="lietotaji"></tbody>
    </table>
</div>

<div class="modal modalLietotajsTabulaAdmin" id="modal-lietotajs-tabula-admin">
    <div class="modal-box">
        <div class="virsraksts">
            <h2>Lietotāja informācija</h2>
            <div class="close-modal"><i class="fas fa-times"></i></div>
        </div>
        <form id="lietotajaFormaTabulaAdmin">
            <div class="formElements">
                <div class="rinda">
                    <label>Vārds: &nbsp;<span class="sarkans">*</span></label>
                    <input type="text" id="lietVardsTabulaAdmin" name="lietVardsTabulaAdmin" required>
                </div>
                <div class="rinda">
                    <label>Uzvārds: &nbsp;<span class="sarkans">*</span></label>
                    <input type="text" id="lietUzvardsTabulaAdmin" name="lietUzvardsTabulaAdmin" required>
                </div>
                <div class="rinda">
                    <label>Epasts: &nbsp;<span class="sarkans">*</span></label>
                    <input type="text" id="lietEpastsTabulaAdmin" name="lietEpastsTabulaAdmin" required>
                </div>
                <div class="rinda">
                    <label>Tālrunis: &nbsp;<span class="sarkans">*</span></label>
                    <input type="text" id="lietTalrunisTabulaAdmin" name="lietTalrunisTabulaAdmin" required>
                </div>
                <?php
                if ($_SESSION['lietotajaLomaMV'] === 'Administrators'):
                ?>
                    <div class="rinda">
                        <label>Loma: &nbsp;<span class="sarkans">*</span></label>
                        <select id="lomaSelect" name="lomaSelect">
                            <option value="Moderators">Moderators</option>
                            <option value="Lietotājs">Lietotājs</option>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="rinda nomainitParoleTabulaAdminRinda">
                    <label>Nomainīt parole?</label>
                    <select id="nomainitParoleSelectTabulaAdmin" name="nomainitParoleSelectTabulaAdmin">
                        <option value="ne">Nē</option>
                        <option value="ja">Jā</option>
                    </select>
                </div>
                <div class="rinda nomainitParoleTabulaAdmin">
                    <label>Parole: &nbsp;<span class="sarkans">*</span></label>
                    <input type="password" id="lietParoleTabulaAdmin" name="lietParoleTabulaAdmin">
                </div>
                <div class="rinda nomainitParoleTabulaAdmin">
                    <label>Parole (atkārtoti): &nbsp;<span class="sarkans">*</span></label>
                    <input type="password" id="lietParoleOtraisTabulaAdmin" name="lietParoleOtraisTabulaAdmin">
                </div>
                <div class="rinda nomainitAtteluTabulaAdminRinda">
                    <label>Nomainīt attēlu?</label>
                    <select id="nomainitAtteluSelectTabulaAdmin" name="nomainitAtteluSelectTabulaAdmin">
                        <option value="ne">Nē</option>
                        <option value="ja">Jā</option>
                    </select>
                </div>
                <div class="rinda" id="nomainitAtteluTabulaAdmin">
                    <label>Attēls: &nbsp;<span class="sarkans">*</span></label>
                    <input type="file" id="attelsTabulaAdmin" name="attelsTabulaAdmin" accept="image/png, image/jpeg">
                </div>
                <div class="rinda papildInfoLiet">
                    <label>Atjaunināšanas datums:</label>
                    <p id="atjauninasanasDatums"></p>
                </div>
                <div class="rinda papildInfoLiet">
                    <label>IP adrese:</label>
                    <p name="ipAdrese" id="ipAdrese"></p>
                </div>
                <input type="hidden" id="lietotajs_admin_ID" name="lietotajs_admin_ID">
                <div id="lietFormPazinojums" class="formPazinojums"></div>
            </div>
            <button type="submit" name="lietotajs_tabula_admin_saglabat" id="lietotajs_tabula_admin_saglabat" class="btn">Saglabāt</button>
        </form>
    </div>
</div>

</div>
</body>

</html>