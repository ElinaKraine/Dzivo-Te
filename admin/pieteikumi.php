<?php
$page = "pieteikumi";
require "assets/header.php";
require "database/con_db.php";
?>
<div class="pieteikumi">
    <table>
        <tr class="heading">
            <th>ID</th>
            <th>Mājokļa tips</th>
            <th>Adrese</th>
            <th>Cena (€)</th>
            <th>Lietotājs</th>
            <th>Statuss</th>
            <th>Izveidošanas datums</th>
            <th></th>
        </tr>
        <tbody id="pieteikumi"></tbody>
    </table>
    <div id="pagination-piet" class="pagination-container"></div>
</div>

<div class="modal modalPieteikumi" id="modal-admin-admin-pieteikums">
    <div class="modal-box">
        <div class="virsraksts">
            <h2>Pieteikums</h2>
            <div class="close-modal"><i class="fas fa-times"></i></div>
        </div>
        <form id="pieteikumaFormaAdmin">
            <div class="formElements">
                <div class="rinda">
                    <label>Lietotājs:</label>
                    <input type="text" id="pietlietotajsAdmin" name="pietlietotajsAdmin" disabled>
                </div>
                <div class="rinda">
                    <label>Mājokļa tips:</label>
                    <input type="text" id="pietMajoklaTipsAdmin" name="pietMajoklaTipsAdmin" disabled>
                </div>
                <div class="rinda">
                    <label>Adrese:</label>
                    <input type="text" id="pietAdreseAdmin" name="pietAdreseAdmin" disabled>
                </div>
                <div class="rinda">
                    <label>Cena (€):</label>
                    <input type="text" id="pietCenaAdmin" name="pietCenaAdmin" disabled>
                </div>
                <div class="rinda">
                    <label>Izveidošanas datums:</label>
                    <input type="text" id="pietDatumsAdmin" name="pietDatumsAdmin" disabled>
                </div>
                <div class="rinda">
                    <label>Statuss: &nbsp;<span class="sarkans">*</span></label>
                    <select id="pietStatussAdmin" name="pietStatussAdmin" required>
                        <option value="iesniegtsPieteikums">Iesniegts pieteikums</option>
                        <option value="pieteikumaParskatisana">Pieteikuma pārskatīšana</option>
                        <option value="majoklaIegadesProcesa">Mājokļa iegādes procesā</option>
                        <option value="majoklisIrIegadats">Mājoklis ir iegādāts</option>
                        <option value="atteikums">Atteikums</option>
                    </select>
                </div>
                <div class="rinda papildInfoLiet">
                    <label>Atjaunināšanas datums:</label>
                    <p id="atjauninasanasDatumsPiet"></p>
                </div>
                <div class="rinda papildInfoLiet">
                    <label>IP adrese:</label>
                    <p name="ipAdresePiet" id="ipAdresePiet"></p>
                </div>
                <input type="hidden" id="piet_ID">
            </div>
            <button type="submit" name="pieteikums_saglabat_admin" id="pieteikums_saglabat_admin" class="btn">Saglabāt</button>
        </form>
    </div>
</div>

</div>
</body>

</html>