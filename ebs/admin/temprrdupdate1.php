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
        echo 'Please enter a valid Quantity.';
    } else {
        if($qty > 0) {
            $insertTempDet = "UPDATE t_temprrd SET qty='$qty' WHERE no_tran='$lcref' AND cd_item='$lcrefdet' AND cd_user='$lccd_user'";
            mysqli_query($conn,$insertTempDet);
        } else {
            $deleteTempDet = "DELETE FROM t_temprrd WHERE no_tran='$lcref' AND cd_item='$lcrefdet' AND cd_user='$lccd_user'";
            mysqli_query($conn, $deleteTempDet);
        }
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>