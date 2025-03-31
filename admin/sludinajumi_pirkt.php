<?php
$page = "pardosanas";
require "assets/header.php";
require "database/con_db.php";
?>
<div class="lietotaji">
    <table>
        <tr class="heading">
            <th>Mājokļa tips</th>
            <th>Īpašnieks</th>
            <th>Adrese</th>
            <th>Cena</th>
            <th>Platība</th>
            <th>Zemes platība</th>
            <th>Istabu skaits</th>
            <th>Stāvu skaits</th>
            <th>Izveidošanas datums</th>
            <th>Statuss</th>
            <th class="thButton">Rediģet</th>
            <th class="thButton">Dzēst</th>
        </tr>
        <?php
        $pirkt_sludinajumi_SQL = "SELECT * FROM majuvieta_pirkt ORDER BY izveidosanas_datums DESC";
        $atlasa_pirkt_sludinajumi_SQL = mysqli_query($savienojums, $pirkt_sludinajumi_SQL);

        while ($ieraksts = mysqli_fetch_array($atlasa_pirkt_sludinajumi_SQL)) {
            echo "
                    <tr>
                        <td>{$ieraksts['majokla_tips']}</td>
                        <td>{$ieraksts['id_ipasnieks']}</td>
                        <td>{$ieraksts['id_adrese']}</td>
                        <td>{$ieraksts['cena']}</td>
                        <td>{$ieraksts['platiba']}</td>
                        <td>{$ieraksts['zemes_platiba']}</td>
                        <td>{$ieraksts['istabas']}</td>
                        <td>{$ieraksts['stavs_vai_stavi']}</td>
                        <td>{$ieraksts['izveidosanas_datums']}</td>
                        <td>{$ieraksts['statuss']}</td>
                        <td>
                            <form method='POST' action='edit_lietotaju.php'>
                                <button type='submit' name='apskatitIeraksts' class='Tbtn' value='{$ieraksts['pirkt_id']}'><i class='fas fa-edit'></i></button>
                            </form>
                        </td>
                        <td>
                            <form method='POST' onsubmit='return confirm(\"Vai tiešām vēlēs dzēst?\");'>
                                <input type='hidden' name='delete_id' value='{$ieraksts['pirkt_id']}'>
                                <button type='submit' name='nodzestIeraksts' class='Tbtn' value='{$ieraksts['pirkt_id']}'><i class='fas fa-trash'></i></button>
                            </form>
                        </td>
                    </tr>
                ";
        }
        ?>
        <tr>
            <td colspan="6">
                <form method='POST' action='pievienot_lietotaju.php'>
                    <button type='submit' class="btn"><i class="fas fa-add"></i></button>
                </form>
            </td>
        </tr>
    </table>
    <?php
    if (isset($_POST['nodzestIeraksts'])) {
        $id = $_POST['delete_id'];
        $sql = "UPDATE itspeks_aktualitates SET Izdzests = 1 WHERE lietotaja_id = '$id'";
        mysqli_query($savienojums, $sql);
        echo "<script>
                window.location.href = window.location.href;
                if(window.performance){
                    if(window.performance.navigation.type == 1){
                        location.reload(true);
                    }
                }
            </script>";
    }
    ?>
</div>
<?php
require "assets/footer.php";
?>