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
    </div>
</div>

<?php
require "assets/footer.php";
?>