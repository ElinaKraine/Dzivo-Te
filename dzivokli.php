<?php
$page = "dzivokli";
require "assets/header.php";
?>

<div class="galvenieFiltri">
    <div class="darijumuVeids">
        <button type="button" name="pirktDziv" class="atlasitsDziv">Pirkt</button>
        <button type="button" name="iretDziv" class="neAtlasitsDziv">Īre</button>
    </div>

    <?php
    $saglabatieId = [];
    if (isset($_SESSION['lietotajaIdDt'])) {
        require_once "admin/database/con_db.php";

        $lietotaja_id = $_SESSION['lietotajaIdDt'];
        $vaicajums = $savienojums->prepare("SELECT id_sludinajums FROM dzivote_saglabatie WHERE id_lietotajs = ?");
        $vaicajums->bind_param("i", $lietotaja_id);
        $vaicajums->execute();
        $rezultats = $vaicajums->get_result();
        while ($ieraksts = $rezultats->fetch_assoc()) {
            $saglabatieId[] = $ieraksts['id_sludinajums'];
        }
        $vaicajums->close();
    }
    ?>

    <div id="contentContainerDzivokli">
    </div>
</div>

<?php
require "assets/footer.php";
?>