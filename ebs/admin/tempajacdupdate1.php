<?php
session_start();
if(isset($_SESSION['ref']) && isset($_SESSION['refdet1']) && isset($_SESSION['refdet2']) && isset($_POST['txtqty'])) {
    include_once 'includes/server.php';
    
    $lcref = $_SESSION['ref'];
    $lcrefdet1 = $_SESSION['refdet1'];
    $lcrefdet2 = $_SESSION['refdet2'];
    $qty = (empty($_POST['txtqty']) ? 0.00 : $_POST['txtqty']);

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if($qty == 0.00) {
        $deleteTempDet = "DELETE FROM t_tempajd WHERE no_tran='$lcref' AND cd_item='$lcrefdet1' AND cd_user='$lccd_user'".(!empty($lcrefdet2) ? " AND cd_stock='$lcrefdet2'" : "");;
        mysqli_query($conn, $deleteTempDet);
    } else {
        $insertTempDet = "UPDATE t_tempajd SET qty='$qty' WHERE no_tran='$lcref' AND cd_item='$lcrefdet1' AND cd_user='$lccd_user'".(!empty($lcrefdet2) ? " AND cd_stock='$lcrefdet2'" : "");
        mysqli_query($conn,$insertTempDet);
    }
    $lcref ='';
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>