<?php
$page = "saglabati";
require "assets/header.php";

if (!isset($_SESSION['lietotajaLomaMV'])) {
    header("Location: index.php");
    exit();
}
?>

<div class="kontainerSaglabatie">
    <h1>Saglabātie sludinājumi</h1>
    <div class="sludinajumi" id="saglabatie">

    </div>
</div>

<?php
require "assets/footer.php";
?>