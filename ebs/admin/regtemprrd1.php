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
        $qty = 1;

        date_default_timezone_set('Asia/Manila');
        $ltt_stamp = date('Y-m-d H:i:s');

        $sql = "UPDATE t_temprr SET dt_tran='$dt_tran',ref_no='$ref_no',cd_brnch='$cd_brnch' WHERE cd_user='$lccd_user' AND no_tran='$lcref'";
        mysqli_query($conn,$sql);
        if(empty($cd_brnch)) {
            echo 'Please select Branch.';
        } else {
            if(empty($cd_item)) { 
                echo 'Please enter a valid Item Code, Serial, or IMEI No.';
            } else {
                $itemSql = "SELECT a.cd_item, b.uprc FROM r_item a LEFT JOIN r_unit b ON a.cd_unit=b.cd_unit WHERE a.cd_item='$cd_item' OR a.serial_no='$cd_item' OR a.imei1_no='$cd_item' OR a.imei2_no='$cd_item'";
                $itemResult = mysqli_query($conn, $itemSql);
                $itemResultCheck = mysqli_num_rows($itemResult);
                if($itemResultCheck > 0) {
                    $itemRow = mysqli_fetch_assoc($itemResult);
                    $lccd_item = $itemRow['cd_item'];
                    $lnuprc = $itemRow['uprc'];
                    $tempDetSql = "SELECT cd_user, cd_item FROM t_temprrd WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran='$lcref'";
                    $tempDetResult = mysqli_query($conn,$tempDetSql);
                    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                    if($tempDetResultCheck > 0) {
                        switch(substr($cd_item,0,2)) {
                            case 'KL':
                                echo "Duplicate Item found.";
                                break;
                            case 'AC':
                                $updateTempDet = "UPDATE t_temprrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran='$lcref'";
                                mysqli_query($conn, $updateTempDet);
                                break;
                            default:
                                echo "Invalid Item Code.";
                                break;
                        }
                    } else {
                        $tranDetSql = "SELECT no_tran, cd_item FROM t_rrd WHERE no_tran!='$lcref' AND cd_item='$lccd_item'";
                        $tranDetResult = mysqli_query($conn,$tranDetSql);
                        $tranDetResultCheck = mysqli_num_rows($tranDetResult);
                        if($tranDetResultCheck > 0) {
                            $tranDetRow = mysqli_fetch_assoc($tranDetResult);
                            echo 'This item is already received before. Please report to the System Administrator.';
                        } else {
                            $insertTempDet = "INSERT INTO t_temprrd(cd_user,no_tran,cd_item,qty,uprc) VALUES('$lccd_user','$lcref','$lccd_item','$qty','$lnuprc')";
                            mysqli_query($conn,$insertTempDet);
                        }
                    }
                } else {
                    echo 'Invalid Item Code, Serial, or IMEI No.';
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