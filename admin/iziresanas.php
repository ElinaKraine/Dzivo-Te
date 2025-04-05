<?php
$page = "ires";
require "assets/header.php";
require "database/con_db.php";
?>
<div class="iziresanas">
    <div class="pievienotKaste">
        <form method='POST' action='pievienot_sludinajumu_pirkt.php'>
            <button type='submit' class="pievienotBtn"><i class="fas fa-add"></i> Pievienot īres ierakstu</button>
        </form>
    </div>
    <table>
        <tr class="heading">
            <th>Mājoklis</th>
            <th>Lietotājs</th>
            <th>Iznomāts no</th>
            <th>Iznomāts līdz</th>
            <th>Cena</th>
            <th>Izveidošanas datums</th>
            <th></th>
        </tr>
        <?php
        $nomas_ieraksti_SQL = "SELECT * FROM majuvieta_iziresana ORDER BY izveidosanas_datums DESC";
        $atlasa_nomas_ieraksti_SQL = mysqli_query($savienojums, $nomas_ieraksti_SQL);

        while ($ieraksts = mysqli_fetch_array($atlasa_nomas_ieraksti_SQL)) {
            $formatted_datums = date('d.m.Y H:i', strtotime($ieraksts['izveidosanas_datums']));
            $formatets_no = date('d.m.Y', strtotime($ieraksts['registresanas_datums']));
            $formatets_lidz = date('d.m.Y', strtotime($ieraksts['izrakstisanas_datums']));

            echo "
                    <tr>
                        <td>{$ieraksts['id_majuvieta_iret']}</td>
                        <td>{$ieraksts['id_lietotajs']}</td>
                        <td>$formatets_no</td>
                        <td>$formatets_lidz</td>
                        <td>{$ieraksts['cena']}</td>
                        <td>$formatted_datums</td>
                        <td class='ierakstaDarbibas'>
                            <form method='POST' action='edit_pieteikumu.php'>
                                <button type='submit' name='apskatitIeraksts' class='editBtn' value='{$ieraksts['iziresana_id']}'><i class='fas fa-edit'></i></button>
                            </form>

                            <form method='POST' onsubmit='return confirm(\"Vai tiešām vēlēs dzēst?\");'>
                                <input type='hidden' name='delete_id' value='{$ieraksts['iziresana_id']}'>
                                <button type='submit' name='nodzestIeraksts' class='deleteBtn' value='{$ieraksts['iziresana_id']}'><i class='fas fa-trash'></i></button>
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