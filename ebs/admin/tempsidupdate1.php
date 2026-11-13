<?php
session_start();
if(isset($_SESSION['ref']) && isset($_SESSION['refdet1']) && isset($_SESSION['refdet2']) && isset($_POST['txtqty']) && isset($_POST['txtuprc']) && isset($_POST['txtdisc'])) {
    include_once 'includes/server.php';
    
    $lcref = $_SESSION['ref'];
    $lcrefdet1 = $_SESSION['refdet1'];
    $lcrefdet2 = $_SESSION['refdet2'];
    $qty = (empty($_POST['txtqty']) ? 0.00 : $_POST['txtqty']);
    $uprc = (empty($_POST['txtuprc']) ? 0.00 : $_POST['txtuprc']);
    $disc = (empty($_POST['txtdisc']) ? 0.00 : $_POST['txtdisc']);

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if($uprc <= 0) {
        echo "Please enter a valid Price.";
    } else {
        if(($qty < 0)) {
            echo 'Please enter a valid Quantity.';
        } else {
            $tempDetSql = "SELECT * FROM t_tempsid WHERE no_tran='$lcref' AND cd_item='$lcrefdet1' AND cd_stock='$lcrefdet2' AND cd_user='$lccd_user'";
            $tempDetResult = mysqli_query($conn, $tempDetSql);
            $tempDetResultCheck = mysqli_num_rows($tempDetResult);
            if($tempDetResultCheck > 0) {
                $tempDetRow = mysqli_fetch_assoc($tempDetResult);
                $lnamount = ($qty * $uprc);
                if($disc <= $lnamount) {
                    $lntotal = ($lnamount - $disc);
                    if($qty > 0) {
                        if($qty > 1 && substr($lcrefdet1,0,2)!='AC') {
                            echo "Products Quantity cannot be modified.";
                        } else {
                            $insertTempDet = "UPDATE t_tempsid SET qty='$qty', uprc='$uprc', amount='$lnamount', disc='$disc' WHERE no_tran='$lcref' AND cd_item='$lcrefdet1' AND cd_stock='$lcrefdet2' AND cd_user='$lccd_user'";
                            mysqli_query($conn,$insertTempDet);
                        }
                    } else {
                        $deleteTempDet = "DELETE FROM t_tempsid WHERE no_tran='$lcref' AND cd_item='$lcrefdet1' AND cd_stock='$lcrefdet2' AND cd_user='$lccd_user'";
                        mysqli_query($conn, $deleteTempDet);
                    }
                } else {
                    echo "Discount cannot be exceeded in Amount.";
                }
            } else {
                echo "Reference ID could not be found.";
            }
            $lcref ='';
        }
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>