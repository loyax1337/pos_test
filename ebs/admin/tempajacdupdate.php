<?php
session_start();
if(isset($_SESSION['ref']) && isset($_POST['txtqty'])) {
    include_once 'includes/server.php';
    
    $lcref = $_SESSION['ref'];
    $qty = (empty($_POST['txtqty']) ? 0.00 : $_POST['txtqty']);

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if($qty == 0.00) {
        $deleteTempDet = "DELETE FROM t_tempajd WHERE cd_item='$lcref' AND cd_user='$lccd_user' AND no_tran IS NULL";
        mysqli_query($conn, $deleteTempDet);
    } else {
        $insertTempDet = "UPDATE t_tempajd SET qty='$qty' WHERE cd_item='$lcref' AND cd_user='$lccd_user'";
        mysqli_query($conn,$insertTempDet);
    }
    $lcref ='';
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>