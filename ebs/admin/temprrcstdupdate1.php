<?php
session_start();
if(isset($_SESSION['id'])) {
    $lccd_user = $_SESSION['id'];
    $lcref = $_SESSION['ref'];
    $lcrefdet = $_SESSION['refdet'];
 
    if(isset($_POST['txtqty']) && isset($_POST['txtucst'])) {
        include_once 'includes/server.php';
        
        $qty = (empty($_POST['txtqty']) ? 0.00 : $_POST['txtqty']);
        $ucst = (empty($_POST['txtucst']) ? 0.00 : $_POST['txtucst']);
        $amt_cst = ($qty * $ucst);

        date_default_timezone_set('Asia/Manila');
        $ltt_stamp = date('Y-m-d H:i:s');
        if(($qty <= 0)) {
            echo 'Please enter a valid Quantity.';
        } else {
            if($ucst <= 0) {
                echo "Please enter a valid Cost.";
            } else {
                $insertTempDet = "UPDATE t_temprrd a LEFT JOIN r_item b ON a.cd_item=b.cd_item SET a.ucst='$ucst', a.amt_cst=(qty * '$ucst') WHERE a.no_tran='$lcref' AND a.cd_user='$lccd_user' AND CONCAT(b.cd_supp,b.cd_model,b.cd_rom)='$lcrefdet'";
                mysqli_query($conn,$insertTempDet);
            }
            $lcref ='';
        }
        mysqli_close($conn);
    } else {
        echo 'Invalid Url Parameters.';
    }   
} else {
    echo "Session Expired. Please login again.";
}
?>