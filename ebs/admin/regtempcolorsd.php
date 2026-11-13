<?php
session_start();

if(isset($_SESSION['id'])) {
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];

    if(isset($_POST['txtnm_colors']) && isset($_POST['cbocd_color'])) {
        include_once 'includes/server.php';
        $nm_colors = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_colors']));
        $cd_color = (!empty($_POST['cbocd_color']) ? strtoupper(mysqli_real_escape_string($conn,$_POST['cbocd_color'])) : '');

        date_default_timezone_set('Asia/Manila');
        $ltt_stamp = date('Y-m-d H:i:s');

        $sql = "UPDATE t_tempcolors SET nm_colors='$nm_colors' WHERE cd_user='$lccd_user' AND cd_colors IS NULL";
        mysqli_query($conn,$sql);
        if(empty($nm_colors)) {
            echo 'Please input Color Variations Name.';
        } else {
            $sql = "SELECT cd_color FROM r_color WHERE cd_color='$cd_color'";
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck > 0) {
                $row = mysqli_fetch_assoc($result);
                $sql = "SELECT cd_user, cd_color FROM t_tempcolorsd WHERE cd_user='$lccd_user' AND cd_color='$cd_color' AND cd_colors IS null";
                $result = mysqli_query($conn,$sql);
                $resultCheck = mysqli_num_rows($result);
                if($resultCheck > 0) {
                    echo 'Duplicate Color found.';
                } else {
                    $sql = "INSERT INTO t_tempcolorsd(cd_user,cd_color) VALUES('$lccd_user','$cd_color')";
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