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
    if(($qty < 0)) {
        echo 'Please enter a valid Quantity.';
    } else {
        if($qty > 0) {
            $insertTempDet = "UPDATE t_tempprd SET qty='$qty' WHERE no_tran='$lcref' AND cd_item='$lcrefdet1' AND cd_user='$lccd_user'".(!empty($lcrefdet2) ? " AND cd_stock='$lcrefdet2'" : " AND (cd_stock IS NULL OR cd_stock='')");
            mysqli_query($conn,$insertTempDet);
        } else {
            $deleteTempDet = "DELETE FROM t_tempprd WHERE no_tran='$lcref' AND cd_item='$lcrefdet1' AND cd_user='$lccd_user'".(!empty($lcrefdet2) ? " AND cd_stock='$lcrefdet2'" : " AND (cd_stock IS NULL OR cd_stock='')");
            mysqli_query($conn, $deleteTempDet);
        }
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>