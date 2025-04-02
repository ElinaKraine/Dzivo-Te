<?php
$page = "lietotaji";
require "assets/header.php";
require "database/con_db.php";
?>
<div class="lietotaji">
    <table>
        <tr class="heading">
            <th>Vārds</th>
            <th>Uzvārds</th>
            <th>E-pasts</th>
            <th>Tālrunis</th>
            <th>Loma</th>
            <th>Izveidošanas datums</th>
            <th class="thButton">Rediģet</th>
            <th class="thButton">Dzēst</th>
        </tr>
        <?php
        $lietotaji_SQL = "SELECT * FROM majuvieta_lietotaji  WHERE izdzests != 1 ORDER BY izveidosanas_datums DESC";
        $atlasa_lietotaji_SQL = mysqli_query($savienojums, $lietotaji_SQL);

        while ($ieraksts = mysqli_fetch_array($atlasa_lietotaji_SQL)) {
            echo "
                    <tr>
                        <td>{$ieraksts['vards']}</td>
                        <td>{$ieraksts['uzvards']}</td>
                        <td>{$ieraksts['epasts']}</td>
                        <td>{$ieraksts['talrunis']}</td>
                        <td>{$ieraksts['loma']}</td>
                        <td>{$ieraksts['izveidosanas_datums']}</td>
                        <td>
                            <form method='POST' action='edit_lietotaju.php'>
                                <button type='submit' name='apskatitIeraksts' class='Tbtn' value='{$ieraksts['lietotaja_id']}'><i class='fas fa-edit'></i></button>
                            </form>
                        </td>
                        <td>
                            <form method='POST' onsubmit='return confirm(\"Vai tiešām vēlēs dzēst?\");'>
                                <input type='hidden' name='delete_id' value='{$ieraksts['lietotaja_id']}'>
                                <button type='submit' name='nodzestIeraksts' class='Tbtn' value='{$ieraksts['lietotaja_id']}'><i class='fas fa-trash'></i></button>
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
</div>
</body>

</html>