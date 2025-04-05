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
            <div class="kasteCentra">
                <div class="profilaAttela">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h2>Sveiks, Jānis Ozols!</h2>
            </div>
            <div class="kastite">
                <p><i class="fa-solid fa-envelope"></i> janis.ozols@gmail.com</p>
                <p><i class="fa-solid fa-phone"></i> +371 20000</p>
                <a href="./" class="btn">Rediģēt profilu</a>
            </div>
            <a class="btn" href="admin/database/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Izlogoties</a>
        </div>
        <div class="tabulas">

        </div>
    </div>
</div>

<?php
require "assets/footer.php";
?>