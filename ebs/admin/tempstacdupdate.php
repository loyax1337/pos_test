<?php
session_start();
if(isset($_SESSION['ref']) && isset($_POST['txtqty'])) {
    include_once 'includes/server.php';
    
    $lcref = $_SESSION['ref'];
    $qty = (empty($_POST['txtqty']) ? 0.00 : $_POST['txtqty']);

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if(($qty < 0)) {
        echo 'Please enter a valid Quantity.';
    } else {
        if($qty > 0) {
            $updateTempDet = "UPDATE t_tempstd SET qty='$qty' WHERE cd_item='$lcref' AND cd_user='$lccd_user' AND ty_tran='AC' AND no_tran IS NULL";
            mysqli_query($conn, $updateTempDet);
        } else{
            $deleteTempDet = "DELETE FROM t_tempstd WHERE cd_item='$lcref' AND cd_user='$lccd_user' AND ty_tran='AC' AND no_tran IS NULL";
            mysqli_query($conn, $deleteTempDet);
        }
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>