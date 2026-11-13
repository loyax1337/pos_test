<?php
session_start();
if(isset($_SESSION['id'])) {
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    $lccd_user = $_SESSION['id'];

    if(isset($_SESSION['ref']) && isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch']) && isset($_POST['txtcd_item'])) {
        $lcref = $_SESSION['ref'];
        include_once 'includes/server.php';

        $dt_tran = $_POST['txtdt_tran'];
        $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
        $cd_brnch = (!empty($_POST['cbocd_brnch']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch'])) : '');
        $cd_item = (!empty($_POST['txtcd_item']) ? mysqli_real_escape_string($conn,$_POST['txtcd_item']) : '');

        date_default_timezone_set('Asia/Manila');
        $ltt_stamp = date('Y-m-d H:i:s');

        $sql = "UPDATE t_temprr SET dt_tran='$dt_tran',ref_no='$ref_no',cd_brnch='$cd_brnch' WHERE cd_user='$lccd_user' AND ty_tran='AC' AND no_tran='$lcref'";
        mysqli_query($conn,$sql);
        if(empty($cd_brnch)) {
            echo 'Please select Branch.';
        } else {
            if(empty($cd_item)) { 
                echo 'Please enter a valid Item Code.';
            } else {
                $itemSql = "SELECT cd_item FROM r_item WHERE cd_item='$cd_item'";
                $itemResult = mysqli_query($conn, $itemSql);
                $itemResultCheck = mysqli_num_rows($itemResult);
                if($itemResultCheck > 0) {
                    $itemRow = mysqli_fetch_assoc($itemResult);
                    $lccd_item = $itemRow['cd_item'];
                    $tempDetSql = "SELECT cd_user, cd_item FROM t_temprrd WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND ty_tran='AC' AND no_tran='$lcref'";
                    $tempDetResult = mysqli_query($conn,$tempDetSql);
                    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                    if($tempDetResultCheck > 0) {
                        //Do nothing...
                    } else {
                        $insertTempDet = "INSERT INTO t_temprrd(ty_tran,cd_user,no_tran,cd_item) VALUES('AC','$lccd_user','$lcref','$lccd_item')";
                        mysqli_query($conn,$insertTempDet);
                    }
                } else {
                    echo 'Invalid Item Code.';
                }
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