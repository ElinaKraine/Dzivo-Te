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
</div>
</body>

</html>