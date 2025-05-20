<?php
$page = "ires";
require "assets/header.php";
require "database/con_db.php";
?>
<div class="iziresanas">
    <table>
        <tr class="heading">
            <th>ID</th>
            <th>Mājokļa tips</th>
            <th>Adrese</th>
            <th>Lietotājs</th>
            <th>Iznomāts no</th>
            <th>Iznomāts līdz</th>
            <th>Cena (€)</th>
            <th>Izveidošanas datums</th>
            <th></th>
        </tr>
        <tbody id="iresIeraksti"></tbody>
    </table>
    <div id="pagination-ires" class="pagination-ires"></div>
</div>

<div class="modal modalIresIeraksts" id="modal-ires-ieraksts">
    <div class="modal-box">
        <div class="virsraksts">
            <h2>Īres ieraksta informācija</h2>
            <div class="close-modal"><i class="fas fa-times"></i></div>
        </div>
        <form id="iresIerakstaForma">
            <div class="formElements">
                <div class="rinda">
                    <label>Mājokļa tips:</label>
                    <input type="text" id="majoklaTipsIres" name="majoklaTipsIres" disabled>
                </div>
                <div class="rinda">
                    <label>Adrese:</label>
                    <input type="text" id="adreseIres" name="adreseIres" disabled>
                </div>
                <div class="rinda">
                    <label>Lietotājs:</label>
                    <input type="text" id="lietotajsIres" name="lietotajsIres" disabled>
                </div>
                <div class="rinda">
                    <label>Iznomāts no:</label>
                    <input type="date" id="iznomatsNo" name="iznomatsNo" required>
                </div>
                <div class="rinda">
                    <label>Iznomāts līdz:</label>
                    <input type="date" id="iznomatsLidz" name="iznomatsLidz" required>
                </div>
                <div class="rinda">
                    <label>Cena (€):</label>
                    <input type="number" id="cenaIret" name="cenaIret" required>
                </div>
                <div class="rinda papildInfoLiet">
                    <label>Atjaunināšanas datums:</label>
                    <p id="atjauninasanasDatumsIres"></p>
                </div>
                <div class="rinda papildInfoLiet">
                    <label>IP adrese:</label>
                    <p name="ipAdreseIres" id="ipAdreseIres"></p>
                </div>
                <input type="hidden" id="ires_ieraksts_ID" name="ires_ieraksts_ID">
            </div>
            <button type="submit" name="iret_ieraksts_saglabat" id="iret_ieraksts_saglabat" class="btn">Saglabāt</button>
        </form>
    </div>
</div>
</div>
</body>

</html>