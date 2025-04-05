<?php
$page = "pieteikumi";
require "assets/header.php";
require "database/con_db.php";
?>
<div class="pieteikumi">
    <div class="pievienotKaste">
        <form method='POST' action='pievienot_pieteikumu.php'>
            <button type='submit' class="pievienotBtn"><i class="fas fa-add"></i> Pievienot pieteikumu</button>
        </form>
    </div>
    <table>
        <tr class="heading">
            <th>Mājoklis</th>
            <th>Lietotājs</th>
            <th>Statuss</th>
            <th>Izveidošanas datums</th>
            <th></th>
        </tr>
        <?php
        $pieteikumi_SQL = "SELECT * FROM majuvieta_pieteikumi ORDER BY izveidosanas_datums DESC";
        $atlasa_pieteikumi_SQL = mysqli_query($savienojums, $pieteikumi_SQL);

        while ($ieraksts = mysqli_fetch_array($atlasa_pieteikumi_SQL)) {
            $formatted_datums = date('d.m.Y H:i', strtotime($ieraksts['izveidosanas_datums']));

            echo "
                    <tr>
                        <td>{$ieraksts['id_majuvieta_pirkt']}</td>
                        <td>{$ieraksts['id_lietotajs']}</td>
                        <td>{$ieraksts['statuss']}</td>
                        <td>$formatted_datums</td>
                        <td class='ierakstaDarbibas'>
                            <form method='POST' action='edit_pieteikumu.php'>
                                <button type='submit' name='apskatitIeraksts' class='editBtn' value='{$ieraksts['pieteikums_id']}'><i class='fas fa-edit'></i></button>
                            </form>

                            <form method='POST' onsubmit='return confirm(\"Vai tiešām vēlēs dzēst?\");'>
                                <input type='hidden' name='delete_id' value='{$ieraksts['pieteikums_id']}'>
                                <button type='submit' name='nodzestIeraksts' class='deleteBtn' value='{$ieraksts['pieteikums_id']}'><i class='fas fa-trash'></i></button>
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