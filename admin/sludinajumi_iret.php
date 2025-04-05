<?php
$page = "iziresanas";
require "assets/header.php";
require "database/con_db.php";
?>
<div class="sludinajumi_iret">
    <div class="pievienotKaste">
        <form method='POST' action='pievienot_sludinajumu_iret.php'>
            <button type='submit' class="pievienotBtn"><i class="fas fa-add"></i> Pievienot sludinājumu</button>
        </form>
    </div>
    <table>
        <tr class="heading">
            <th>Mājokļa tips</th>
            <th>Īpašnieks</th>
            <th>Adrese</th>
            <th>€/dienā</th>
            <th>€/nedēļā</th>
            <th>€/mēnesī</th>
            <th>Platība</th>
            <th>Zemes platība</th>
            <th>Istabu skaits</th>
            <th>Stāvu skaits</th>
            <th>Izveidošanas datums</th>
            <th>Statuss</th>
            <th></th>
        </tr>
        <?php
        $pirkt_sludinajumi_SQL = "SELECT * FROM majuvieta_iret ORDER BY izveidosanas_datums DESC";
        $atlasa_pirkt_sludinajumi_SQL = mysqli_query($savienojums, $pirkt_sludinajumi_SQL);

        while ($ieraksts = mysqli_fetch_array($atlasa_pirkt_sludinajumi_SQL)) {
            $formatted_datums = date('d.m.Y H:i', strtotime($ieraksts['izveidosanas_datums']));

            echo "
                    <tr>
                        <td>{$ieraksts['majokla_tips']}</td>
                        <td>{$ieraksts['id_ipasnieks']}</td>
                        <td>{$ieraksts['id_adrese']}</td>
                        <td>{$ieraksts['cena_diena']}</td>
                        <td>{$ieraksts['cena_nedela']}</td>
                        <td>{$ieraksts['cena_menesis']}</td>
                        <td>{$ieraksts['platiba']}</td>
                        <td>{$ieraksts['zemes_platiba']}</td>
                        <td>{$ieraksts['istabas']}</td>
                        <td>{$ieraksts['stavi_vai_stavs']}</td>
                        <td>$formatted_datums</td>
                        <td>{$ieraksts['statuss']}</td>
                        <td class='ierakstaDarbibas'>
                            <form method='POST' action='edit_pieteikumu.php'>
                                <button type='submit' name='apskatitIeraksts' class='editBtn' value='{$ieraksts['iret_id']}'><i class='fas fa-edit'></i></button>
                            </form>

                            <form method='POST' onsubmit='return confirm(\"Vai tiešām vēlēs dzēst?\");'>
                                <input type='hidden' name='delete_id' value='{$ieraksts['iret_id']}'>
                                <button type='submit' name='nodzestIeraksts' class='deleteBtn' value='{$ieraksts['iret_id']}'><i class='fas fa-trash'></i></button>
                            </form>
                        </td>
                    </tr>
                ";
        }
        ?>
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
</div>
</body>

</html>