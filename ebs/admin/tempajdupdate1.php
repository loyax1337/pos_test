<?php
session_start();
if(isset($_SESSION['ref']) && isset($_SESSION['refdet1']) && isset($_SESSION['refdet2']) && isset($_POST['txtqty']) && isset($_POST['txtucst'])) {
    include_once 'includes/server.php';
    
    $lcref = $_SESSION['ref'];
    $lcrefdet1 = $_SESSION['refdet1'];
    $lcrefdet2 = $_SESSION['refdet2'];
    $qty = (empty($_POST['txtqty']) ? 0.00 : $_POST['txtqty']);
    $ucst = (empty($_POST['txtucst']) ? 0.00 : $_POST['txtucst']);
    $amt_cst = ($qty * $ucst);

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if(($qty == 0) || ($ucst < 0)) {
        echo 'Please input required fields completely and valid.';
    } else {
        $sql = "UPDATE t_tempajd SET qty='$qty',ucst='$ucst',amt_cst='$amt_cst' WHERE no_tran='$lcref' AND cd_stock='$lcrefdet2' AND cd_item='$lcrefdet1' AND cd_user='$lccd_user'";
        mysqli_query($conn,$sql);
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>