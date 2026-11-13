<?php
if(isset($_POST['txtnm_roms'])) {
    include_once 'includes/server.php';
    session_start();
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lcref = $_SESSION['ref'];
    $lccd_menu = '008';

    $groupDetSql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
    $groupDetResult = mysqli_query($conn, $groupDetSql);
    $groupDetResultCheck = mysqli_num_rows($groupDetResult);
    if($groupDetResultCheck > 0) {
        $groupDetRow = mysqli_fetch_assoc($groupDetResult);
        $llaction = $groupDetRow['edit'];
        if($llaction=='1') {
            $nm_roms = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_roms']));
            //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');
            if(empty($nm_roms)) {
                echo 'Please input required fields completely.';
            } else {
                $romsSql = "SELECT cd_roms FROM r_roms WHERE cd_roms!='$lcref' AND nm_roms='$nm_roms'";
                $romsResult = mysqli_query($conn, $romsSql);
                $romsResultCheck = mysqli_num_rows($romsResult);
                if($romsResultCheck > 0) {
                    echo "Duplicate record name found.";
                } else {
                    $deleteTranDet = "DELETE FROM r_romsd WHERE cd_roms='$lcref'";
                    mysqli_query($conn, $deleteTranDet);

                    $tempDetSql = "SELECT * FROM t_tempromsd WHERE cd_roms='$lcref' AND cd_user='$lccd_user'";
                    $tempDetResult = mysqli_query($conn,$tempDetSql);
                    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                    if($tempDetResultCheck > 0) {
                        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                            $lccd_rom = $tempDetRow['cd_rom'];

                            $insertTranDet = "INSERT INTO r_romsd(cd_roms,cd_rom) VALUES('$lcref','$lccd_rom')";
                            mysqli_query($conn,$insertTranDet);
                        }
                    }
                    $updateTran = "UPDATE r_roms SET nm_roms='$nm_roms',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE cd_roms='$lcref'";
                    mysqli_query($conn,$updateTran);

                    $deleteTemp="DELETE FROM t_temproms WHERE cd_roms='$lcref' AND cd_user='$lccd_user'";
                    mysqli_query($conn, $deleteTemp);
                    $deleteTempDet="DELETE FROM t_tempromsd WHERE cd_roms='$lcref' AND cd_user='$lccd_user'";
                    mysqli_query($conn, $deleteTempDet);
                }
                $lcref ='';
            }
        } else {
            echo 'Access denied! Group access rights cannot perform this action.';
        }
    } else {
        echo 'Access denied! Group access rights cannot perform this action.';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>