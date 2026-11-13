<?php
session_start();
include_once 'includes/server.php';

if(isset($_SESSION['ref']) && isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch']) && isset($_POST['cbocd_item']) && isset($_POST['txtqty'])){
    $lcref = $_SESSION['ref'];

    $dt_tran = $_POST['txtdt_tran'];
    $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
    $cd_brnch = (!empty($_POST['cbocd_brnch']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch'])) : '');
    $cd_item = (!empty($_POST['cbocd_item']) ? mysqli_real_escape_string($conn,$_POST['cbocd_item']) : '');
    $qty = ($_POST['txtqty'] == 0 ? 0: $_POST['txtqty']);

    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');

    $sql = "UPDATE t_tempaj SET dt_tran='$dt_tran',ref_no='$ref_no',cd_brnch='$cd_brnch' WHERE cd_user='$lccd_user' AND no_tran='$lcref'";
    mysqli_query($conn,$sql);
    if(empty($cd_brnch)) {
        echo 'Please select Branch.';
    } else {
        if(empty($cd_item) || ($qty == 0)) { 
            echo 'Please input required fields completely and valid.';
        } else {
            $sql = "SELECT cd_item FROM r_item WHERE cd_item='$cd_item'";
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck > 0) {
                $row = mysqli_fetch_assoc($result);
                $lccd_item = $row['cd_item'];
                $sql = "SELECT cd_user, cd_item FROM t_tempajd WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran='$lcref'";
                $result = mysqli_query($conn,$sql);
                $resultCheck = mysqli_num_rows($result);
                if($resultCheck > 0) {
                    echo 'Duplicate item found.';
                } else {
                    $sql = "INSERT INTO t_tempajd(no_tran,cd_item,qty,cd_user) VALUES('$lcref','$lccd_item','$qty','$lccd_user')";
                    mysqli_query($conn,$sql);
                }
            } else {
                echo 'Invalid item code.';
            }
        }
    }
} else {
    echo 'Invalid Url Parameters.';
}
mysqli_close($conn);
?>