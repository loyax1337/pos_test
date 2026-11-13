<?php
session_start();
if(isset($_SESSION['id'])) {
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
        
    if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch1']) && isset($_POST['cbocd_brnch2']) && isset($_POST['txtcd_item'])) {
        include_once 'includes/server.php';
        $dt_tran = $_POST['txtdt_tran'];
        $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
        //$cd_brnch1 = (!empty($_POST['cbocd_brnch1']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch1'])) : '');
        $cd_brnch1 = $lccd_brnch;
        $cd_brnch2 = (!empty($_POST['cbocd_brnch2']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch2'])) : '');
        $cd_item = (!empty($_POST['txtcd_item']) ? mysqli_real_escape_string($conn,$_POST['txtcd_item']) : '');

        date_default_timezone_set('Asia/Manila');
        $ltt_stamp = date('Y-m-d H:i:s');

        $sql = "UPDATE t_tempst SET dt_tran='$dt_tran',ref_no='$ref_no',cd_brnch1='$cd_brnch1',cd_brnch2='$cd_brnch2' WHERE cd_user='$lccd_user' AND ty_tran='AC' AND no_tran IS null";
        mysqli_query($conn,$sql);
        if(empty($cd_brnch1)) {
            echo "Please select Origin.";
        } else {
            if(empty($cd_brnch2)) {
                echo "Please select Destination.";
            } else {
                if(empty($cd_item)) {
                    echo 'Please enter a valid Item Code.';
                } else {
                    if(substr($cd_item,0,2) == 'AC') {
                        $itemSql = "SELECT cd_item FROM r_item WHERE cd_item='$cd_item'";
                        $itemResult = mysqli_query($conn, $itemSql);
                        $itemResultCheck = mysqli_num_rows($itemResult);
                        if($itemResultCheck > 0) {
                            $itemRow = mysqli_fetch_assoc($itemResult);
                            $lccd_item = $itemRow['cd_item'];
                            $sql = "SELECT cd_user, cd_item FROM t_tempstd WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND ty_tran='AC' AND no_tran IS null";
                            $result = mysqli_query($conn,$sql);
                            $resultCheck = mysqli_num_rows($result);
                            if($resultCheck > 0) {
                                //Do nothing...
                            } else {
                                $sql = "INSERT INTO t_tempstd(ty_tran,cd_user,cd_item) VALUES('AC','$lccd_user','$lccd_item')";
                                mysqli_query($conn,$sql);
                            }
                        } else {
                            echo 'Invalid item code.';
                        }
                    } else {
                        echo "Invalid Item Code.";
                    }
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