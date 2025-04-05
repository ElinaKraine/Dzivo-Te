<?php
$page = "dzivokli";
require "assets/header.php";
?>

<div class="galvenieFiltri">
    <div class="darijumuVeids">
        <button type="button" name="pirktDziv" class="atlasitsDziv">Pirkt</button>
        <button type="button" name="iretDziv" class="neAtlasitsDziv">Īrēt</button>
    </div>

    <?php
    $saglabatieId = [];
    if (isset($_SESSION['lietotajaIdDt'])) {
        require_once "admin/database/con_db.php";

        $lietotaja_id = $_SESSION['lietotajaIdDt'];
        $stmt = $savienojums->prepare("SELECT id_sludinajums FROM dzivote_saglabatie WHERE id_lietotajs = ?");
        $stmt->bind_param("i", $lietotaja_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $saglabatieId[] = $row['id_sludinajums'];
        }
        $stmt->close();
    }
    ?>

    <script>
        const saglabatieSludinajumi = <?php echo json_encode($saglabatieId); ?>;
    </script>


    <div id="contentContainerDzivokli">
    </div>
</div>

<?php
require "assets/footer.php";
?>