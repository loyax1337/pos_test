<?php
session_start();
include_once 'includes/server.php';

if (isset($_SESSION['ref']) && isset($_POST['txtnm_roms']) && isset($_POST['cbocd_rom'])){
    $lcref = $_SESSION['ref'];

    $nm_roms = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_roms']));
    $cd_rom = (!empty($_POST['cbocd_rom']) ? mysqli_real_escape_string($conn,$_POST['cbocd_rom']) : '');

    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');

    $updateTemp = "UPDATE t_temproms SET nm_roms='$nm_roms' WHERE cd_user='$lccd_user' AND cd_roms='$lcref'";
    mysqli_query($conn,$updateTemp);
    if(empty($nm_roms)) {
        echo 'Please input ROM Variations Name.';
    } else {
        if(empty($cd_rom)) { 
            echo 'Please input required fields completely and valid.';
        } else {
            $romSql = "SELECT cd_rom FROM r_rom WHERE cd_rom='$cd_rom'";
            $romResult = mysqli_query($conn, $romSql);
            $romResultCheck = mysqli_num_rows($romResult);
            if($romResultCheck > 0) {
                $tempDetSql = "SELECT cd_user, cd_rom FROM t_tempromsd WHERE cd_user='$lccd_user' AND cd_rom='$cd_rom' AND cd_roms='$lcref'";
                $tempDetResult = mysqli_query($conn,$tempDetSql);
                $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                if($tempDetResultCheck > 0) {
                    echo 'Duplicate item found.';
                } else {
                    $insertTempDet = "INSERT INTO t_tempromsd(cd_roms,cd_rom,cd_user) VALUES('$lcref','$cd_rom','$lccd_user')";
                    mysqli_query($conn,$insertTempDet);
                }
            } else {
                echo 'Invalid ROM code.';
            }
        }
    }
} else {
    echo 'Invalid Url Parameters.';
}
mysqli_close($conn);
?>