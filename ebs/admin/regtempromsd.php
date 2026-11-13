<?php
session_start();

if(isset($_SESSION['id'])) {
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];

    if(isset($_POST['txtnm_roms']) && isset($_POST['cbocd_rom'])) {
        include_once 'includes/server.php';
        $nm_roms = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_roms']));
        $cd_rom = (!empty($_POST['cbocd_rom']) ? strtoupper(mysqli_real_escape_string($conn,$_POST['cbocd_rom'])) : '');

        date_default_timezone_set('Asia/Manila');
        $ltt_stamp = date('Y-m-d H:i:s');

        $sql = "UPDATE t_temproms SET nm_roms='$nm_roms' WHERE cd_user='$lccd_user' AND cd_roms IS NULL";
        mysqli_query($conn,$sql);
        if(empty($nm_roms)) {
            echo 'Please input ROM Variations Name.';
        } else {
            $sql = "SELECT cd_rom FROM r_rom WHERE cd_rom='$cd_rom'";
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck > 0) {
                $row = mysqli_fetch_assoc($result);
                $sql = "SELECT cd_user, cd_rom FROM t_tempromsd WHERE cd_user='$lccd_user' AND cd_rom='$cd_rom' AND cd_roms IS null";
                $result = mysqli_query($conn,$sql);
                $resultCheck = mysqli_num_rows($result);
                if($resultCheck > 0) {
                    echo 'Duplicate ROM found.';
                } else {
                    $sql = "INSERT INTO t_tempromsd(cd_user,cd_rom) VALUES('$lccd_user','$cd_rom')";
                    mysqli_query($conn,$sql);
                }
            } else {
                echo 'Invalid item code.';
            }
        }
        mysqli_close($conn);
    } else {
        echo 'Invalid Url Parameters.';
    }
} else {
    echo "Session Expired. Please log in again.";
}
?>