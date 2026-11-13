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
            $qty = 1;

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');

            $sql = "UPDATE t_tempst SET dt_tran='$dt_tran',ref_no='$ref_no',cd_brnch1='$cd_brnch1',cd_brnch2='$cd_brnch2' WHERE cd_user='$lccd_user' AND ty_tran='KL' AND no_tran IS null";
            mysqli_query($conn,$sql);
            if(empty($cd_brnch1)) {
                echo "Please select Origin.";
            } else {
                if(empty($cd_brnch2)) {
                    echo "Please select Destination.";
                } else {
                    if(empty($cd_item) || ($qty<=0)) {
                        echo 'Please input required fields completely and valid.';
                    } else {
                        $sql = "SELECT cd_item FROM r_item WHERE cd_item='$cd_item'";
                        $result = mysqli_query($conn, $sql);
                        $resultCheck = mysqli_num_rows($result);
                        if($resultCheck > 0) {
                            $row = mysqli_fetch_assoc($result);
                            $lccd_item = $row['cd_item'];
                            $sql = "SELECT cd_user, cd_item FROM t_tempstd WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND ty_tran='KL' AND no_tran IS null";
                            $result = mysqli_query($conn,$sql);
                            $resultCheck = mysqli_num_rows($result);
                            if($resultCheck > 0) {
                                switch(substr($cd_item,0,2)) {
                                    case 'KL':
                                        echo 'Duplicate item found.';
                                        break;
                                    case 'AC':
                                        $updateTempDet = "UPDATE t_tempstd SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND ty_tran='KL' AND no_tran IS null";
                                        mysqli_query($conn, $updateTempDet);
                                        break;
                                    default:
                                        echo 'Invalid Item Code.';
                                        break;
                                }
                            } else {
                                $sql = "INSERT INTO t_tempstd(ty_tran,cd_user,cd_item,qty) VALUES('KL','$lccd_user','$lccd_item','$qty')";
                                mysqli_query($conn,$sql);
                            }
                        } else {
                            echo 'Invalid item code.';
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