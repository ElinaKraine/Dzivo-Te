<?php
    $page = "majas";
    require "assets/header.php";
?>

<form class="filtri">
    <div class="darijumuVeids">
        <button>Pirkt</button>
        <button></button>
    </div>

</form>
<div class="lielaKreisaPuse">

</div>
<div class="karte">
    <h2>Mājas pārdošanai</h2>
    <form class="kartosana">

    </form>
    <div class="sludinajumasKartinas">
        <select name="" id="">
            <option value="">Kārtot: Cena(Aug - Zem)</option>
            <option value="">Kārtot: Cena(Zem - Aug)</option>
            <option value="">Kārtot: Jaunākie</option>
            <option value="">Kārtot: Platība(Aug - Zem)</option>
            <option value="">Kārtot: Platība(Zem - Aug)</option>
        </select>
    </div>
</div>

<?php
    require "assets/footer.php";
?>