<?php
session_start();
if(isset($_SESSION['ref']) && isset($_SESSION['refdet']) && isset($_POST['txtqty'])) {
    include_once 'includes/server.php';
    
    $lcref = $_SESSION['ref'];
    $lcrefdet = $_SESSION['refdet'];
    $qty = (empty($_POST['txtqty']) ? 0.00 : $_POST['txtqty']);

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if(($qty < 0)) {
        echo 'Please enter a valid Quantity';
    } else {
        if($qty > 0) {
            $updateTempDet = "UPDATE t_tempstd SET qty='$qty' WHERE no_tran='$lcref' AND cd_item='$lcrefdet' AND cd_user='$lccd_user' AND ty_tran='KL'";
            mysqli_query($conn,$updateTempDet);
        } else {
            $deleteTempDet = "DELETE FROM t_tempstd WHERE no_tran='$lcref' AND cd_item='$lcrefdet' AND cd_user='$lccd_user' AND ty_tran='KL'";
            mysqli_query($conn, $deleteTempDet);
        }
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>