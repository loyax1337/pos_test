<?php
    session_start();
    if(isset($_SESSION['id'])) {
        $lccd_group = $_SESSION['group'];
        $lccd_brnch = $_SESSION['branch'];
        $lccd_user = $_SESSION['id'];
        if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch']) && isset($_POST['txtcd_item'])) {
            $dt_tran = $_POST['txtdt_tran'];
            include_once 'includes/server1.php';
            $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
            $cd_brnch = (!empty($_POST['cbocd_brnch']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch'])) : '');
            $cd_item = (!empty($_POST['txtcd_item']) ? mysqli_real_escape_string($conn,$_POST['txtcd_item']) : '');
            $qty = 1;

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');

            $sql = "UPDATE t_tempjwlrr SET dt_tran='$dt_tran',ref_no='$ref_no',cd_brnch='$cd_brnch' WHERE cd_user='$lccd_user' AND no_tran IS null";
            mysqli_query($conn,$sql);
            if(empty($cd_brnch)) {
                echo "Please select Branch.";
            } else {
                if(empty($cd_item)) {
                    echo 'Please input a valid Item Code or GRA Certification.';
                } else {
                    $itemSql = "SELECT a.cd_item, a.jw_code, a.uprc FROM r_itemjwl a LEFT JOIN r_itemjwl b ON a.cd_unit=b.cd_unit WHERE a.cd_item='$cd_item' OR a.jw_code='$cd_item' OR a.rs_size='$cd_item' OR a.carat_no='$cd_item' OR a.grams='$cd_item'";
                    $itemResult = mysqli_query($conn, $itemSql);
                    $itemResultCheck = mysqli_num_rows($itemResult);
                    if($itemResultCheck > 0) {
                        $row = mysqli_fetch_assoc($itemResult);
                        $lccd_item = $row['cd_item'];
                        $lcjw_code = $row['jw_code'];
                        $lnuprc = $row['uprc'];
                        $tempDetSql = "SELECT cd_user, cd_item FROM t_tempjwlrrd WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran IS null";
                        $tempDetResult = mysqli_query($conn,$tempDetSql);
                        $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                        if($tempDetResultCheck > 0) {
                            $tempDetRow = mysqli_fetch_assoc($tempDetResult);
                            switch(substr($cd_item,0,2)) {
                                case 'GA':
                                    $updateTempDet = "UPDATE t_tempjwlrrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran IS null";
                                    mysqli_query($conn, $updateTempDet);
                                    break;
                                case 'YA':
                                    $updateTempDet = "UPDATE t_tempjwlrrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran IS null";
                                    mysqli_query($conn, $updateTempDet);
                                    break;    
                                case 'SN':
                                    $updateTempDet = "UPDATE t_tempjwlrrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran IS null";
                                    mysqli_query($conn, $updateTempDet);
                                    break;    
                                case 'GR':
                                    $updateTempDet = "UPDATE t_tempjwlrrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran IS null";
                                    mysqli_query($conn, $updateTempDet);
                                    break;
                                case 'AC':
                                    $updateTempDet = "UPDATE t_tempjwlrrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran IS null";
                                    mysqli_query($conn, $updateTempDet);
                                    break;    
                                case 'GE':
                                    $updateTempDet = "UPDATE t_tempjwlrrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran IS null";
                                    mysqli_query($conn, $updateTempDet);
                                    break;   
                                case 'HZ':
                                    $updateTempDet = "UPDATE t_tempjwlrrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran IS null";
                                    mysqli_query($conn, $updateTempDet);
                                    break;
                                case 'NP':
                                    $updateTempDet1 = "UPDATE t_tempjwlrrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND jw_code='$lccd_item' AND no_tran IS null";
                                    mysqli_query($conn, $updateTempDet1);
                                    break;
                                case 'YB':
                                    $updateTempDet = "UPDATE t_tempjwlrrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran IS null";
                                    mysqli_query($conn, $updateTempDet);
                                    break;
                                case 'YD':
                                    $updateTempDet = "UPDATE t_tempjwlrrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' AND no_tran IS null";
                                    mysqli_query($conn, $updateTempDet);
                                    break;      
                                case 'Z':
                                    $updateTempDet1 = "UPDATE t_tempjwlrrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND jw_code='$lccd_item' AND no_tran IS null";
                                    mysqli_query($conn, $updateTempDet1);
                                    break;
                                
                                default:
                                    echo "Invalid Item Code or GRA Certificate.";
                                    break;
                            }
                        } else {
                            $insertTempDet = "INSERT INTO t_tempjwlrrd(cd_user,cd_item,jw_code,qty,uprc) VALUES('$lccd_user','$lccd_item','$lcjw_code','$qty','$lnuprc')";
                            mysqli_query($conn,$insertTempDet);
                        }
                    } else {
                        echo "Invalid Item Code or GRA Certificate.";
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