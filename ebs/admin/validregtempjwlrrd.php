<?php
session_start();
$lccd_group = $_SESSION['group'];
$lccd_brnch = $_SESSION['branch'];
$lccd_user = $_SESSION['id'];
        
if(isset($_SESSION['id'])) {
    if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch']) && isset($_POST['txtcd_item'])) {
        $dt_tran = $_POST['txtdt_tran'];
        include_once 'includes/server1.php';
        $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
        $cd_brnch = (!empty($_POST['cbocd_brnch']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch'])) : '');
        $cd_item = (!empty($_POST['txtcd_item']) ? mysqli_real_escape_string($conn,$_POST['txtcd_item']) : '');

        $qty = 1;

        date_default_timezone_set('Asia/Manila');
        $ltt_stamp = date('Y-m-d H:i:s');


        $sql = "UPDATE t_tempjwlrr SET dt_tran='$dt_tran',ref_no='$ref_no',cd_brnch='$lccd_brnch' WHERE cd_user='$lccd_user' AND ty_tran='GRA' AND no_tran IS null";
        mysqli_query($conn,$sql);
        if(empty($cd_brnch)) {
            echo "Please select Branch.";
        } else {
            if(empty($cd_item)) {
                echo "Invalid Item Code, Serial, or IMEI No.";
            } else {
                $itemSql = "SELECT cd_item,jw_code FROM r_itemjwl WHERE cd_item='$cd_item' OR jw_code ='$cd_item' OR rs_size='$cd_item' OR carat_no='$cd_item' OR grams='$cd_item'";
                $itemResult = mysqli_query($conn, $itemSql);
                $itemResultCheck = mysqli_num_rows($itemResult);
                if($itemResultCheck > 0) {
                    $row = mysqli_fetch_assoc($itemResult);
                    $lccd_item = $row['cd_item'];
                    $lcjw_code = $row['jw_code'];
                    $tempDetSql = "SELECT cd_user, cd_item, jw_code FROM t_tempjwlrrd WHERE cd_user='$lccd_user' AND cd_item='$lccd_item' OR jw_code = '$lcjw_code' AND ty_tran='GRA' AND no_tran IS null";
                    $tempDetResult = mysqli_query($conn,$tempDetSql);
                    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                    if($tempDetResultCheck > 0) {
                        $tempDetRow = mysqli_fetch_assoc($tempDetResult);
                        switch(substr($cd_item,0,2)) {
                            case 'GA':
                                echo "Duplicate Item Found.";
                                break;
                            case 'SN':
                                //Do nothing...
                                break;
                            case 'YA':
                                //Do nothing...
                                break;    
                            case 'YB':
                                //Do nothing...
                                break;
                            case 'YD':
                                //Do nothing...
                                break;    
                            case 'AC':
                                //Do nothing...
                                break;                                
                            case 'GR':
                                echo "Duplicate Item Found.";
                                break;
                            case 'NP':
                                echo "Duplicate Item Found.";
                                break;
                            case 'GE':
                                echo "Duplicate Item Found.";
                                break;
                            case 'GEL':
                                echo "Duplicate Item Found.";
                                break;    
                            case 'Z':
                                echo "Duplicate Item Found.";
                                break;
                            default:
                                echo "Invalid Item Code or GRA Certificate.";
                                break;
                        }
                    } else {
                        $tranDetSql = "SELECT no_tran, cd_item, jw_code FROM t_jwlrrd WHERE cd_item='$lccd_item' OR jw_code='$lccd_item'";
                        $tranDetResult = mysqli_query($conn,$tranDetSql);
                        $tranDetResultCheck = mysqli_num_rows($tranDetResult);
                        if($tranDetResultCheck > 0) {
                            $tranDetRow = mysqli_fetch_assoc($tranDetResult);
                            echo '1';
                        }
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