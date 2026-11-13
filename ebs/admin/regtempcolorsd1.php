<?php
session_start();
include_once 'includes/server.php';

if (isset($_SESSION['ref']) && isset($_POST['txtnm_colors']) && isset($_POST['cbocd_color'])){
    $lcref = $_SESSION['ref'];

    $nm_colors = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_colors']));
    $cd_color = (!empty($_POST['cbocd_color']) ? mysqli_real_escape_string($conn,$_POST['cbocd_color']) : '');

    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');

    $updateTemp = "UPDATE t_tempcolors SET nm_colors='$nm_colors' WHERE cd_user='$lccd_user' AND cd_colors='$lcref'";
    mysqli_query($conn,$updateTemp);
    if(empty($nm_colors)) {
        echo 'Please input Color Variations Name.';
    } else {
        if(empty($cd_color)) { 
            echo 'Please input required fields completely and valid.';
        } else {
            $colorSql = "SELECT cd_color FROM r_color WHERE cd_color='$cd_color'";
            $colorResult = mysqli_query($conn, $colorSql);
            $colorResultCheck = mysqli_num_rows($colorResult);
            if($colorResultCheck > 0) {
                $tempDetSql = "SELECT cd_user, cd_color FROM t_tempcolorsd WHERE cd_user='$lccd_user' AND cd_color='$cd_color' AND cd_colors='$lcref'";
                $tempDetResult = mysqli_query($conn,$tempDetSql);
                $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                if($tempDetResultCheck > 0) {
                    echo 'Duplicate item found.';
                } else {
                    $insertTempDet = "INSERT INTO t_tempcolorsd(cd_colors,cd_color,cd_user) VALUES('$lcref','$cd_color','$lccd_user')";
                    mysqli_query($conn,$insertTempDet);
                }
            } else {
                echo 'Invalid Color code.';
            }
        }
    }
} else {
    echo 'Invalid Url Parameters.';
}
mysqli_close($conn);
?>