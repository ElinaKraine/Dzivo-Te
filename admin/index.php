<?php
$page = "sakums";
require "assets/header.php";
?>
<div class="indexKaste">
    <div class="lielaKasteIndex">
        <div class="augsup">
            <div class="lietotajaInfo" id="profila_admin_info"></div>
            <div id="dazada_info"></div>
        </div>
        <div class="lejup">
            <div id="grafiks"></div>
        </div>
    </div>
    <div class="mazaKasteIndex">
        <div class="virsraksts">
            Lietotāji
        </div>
        <div id="lietotaji_tabula_admin"></div>
    </div>
</div>

<div class="modal modalLietotajsAdmin" id="modal-lietotajs-admin">
    <div class="modal-box">
        <div class="virsraksts">
            <h2>Lietotāja informācija</h2>
            <div class="close-modal"><i class="fas fa-times"></i></div>
        </div>
        <form id="lietotajaFormaAdmin">
            <div class="formElements">
                <div class="rinda">
                    <label>Vārds:</label>
                    <input type="text" id="lietVardsAdmin" name="lietVardsAdmin" required>
                </div>
                <div class="rinda">
                    <label>Uzvārds:</label>
                    <input type="text" id="lietUzvardsAdmin" name="lietUzvardsAdmin" required>
                </div>
                <div class="rinda">
                    <label>Epasts:</label>
                    <input type="text" id="lietEpastsAdmin" name="lietEpastsAdmin" required>
                </div>
                <div class="rinda">
                    <label>Tālrunis:</label>
                    <input type="text" id="lietTalrunisAdmin" name="lietTalrunisAdmin" required>
                </div>
                <div class="rinda">
                    <label>Nomainīt parole?</label>
                    <select id="nomainitParoleSelectAdmin" name="nomainitParoleAdmin">
                        <option value="ne">Nē</option>
                        <option value="ja">Jā</option>
                    </select>
                </div>
                <div class="rinda nomainitParoleAdmin">
                    <label>Parole:</label>
                    <input type="password" id="lietParoleAdmin" name="lietParoleAdmin">
                </div>
                <div class="rinda nomainitParoleAdmin">
                    <label>Parole (atkārtoti):</label>
                    <input type="password" id="lietParoleOtraisAdmin" name="lietParoleOtraisAdmin">
                </div>
                <div class="rinda">
                    <label>Nomainīt attēlu?</label>
                    <select id="nomainitAtteluSelectAdmin" name="nomainitAtteluAdmin">
                        <option value="ne">Nē</option>
                        <option value="ja">Jā</option>
                    </select>
                </div>
                <div class="rinda" id="nomainitAtteluAdmin">
                    <label>Attēls:</label>
                    <input type="file" id="attelsAdmin" name="attelsAdmin" accept="image/png, image/jpeg">
                </div>
                <div class="rinda">
                    <input type="hidden" id="liet_admin_ID" name="liet_admin_ID">
                </div>
            </div>
            <button type="submit" name="lietotajs_admin_saglabat" id="lietotajs_admin_saglabat" class="btn">Saglabāt</button>
        </form>
    </div>
</div>

</div>
</body>

</html>