<?php
    session_start();

    if(isset($_SESSION['id'])) {
        $lccd_group = $_SESSION['group'];
        $lccd_brnch = $_SESSION['branch'];
        $lccd_user = $_SESSION['id'];
        if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch']) && isset($_POST['edtremarks']) && isset($_POST['txtcd_item'])) {
            $dt_tran = $_POST['txtdt_tran'];
            include_once 'includes/server.php';
            $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
            $cd_brnch = $lccd_brnch;
            $remarks = ltrim(mysqli_real_escape_string($conn, $_POST['edtremarks']));
            $cd_item = (!empty($_POST['txtcd_item']) ? mysqli_real_escape_string($conn,$_POST['txtcd_item']) : '');
            $qty = 1;

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');

            $sql = "UPDATE t_tempsr SET dt_tran='$dt_tran',ref_no='$ref_no',cd_brnch='$cd_brnch',remarks='$remarks' WHERE cd_user='$lccd_user' AND no_tran IS null";
            mysqli_query($conn,$sql);
            if(empty($cd_brnch)) {
                echo "Please select Branch.";
            } else {
                if(empty($cd_item)) {
                    echo 'Please input a valid Item Code, Serial, or IMEI No.';
                } else {
                    $tempDetSql = "SELECT * FROM t_tempsrd WHERE cd_user='$lccd_user' AND no_tran IS null";
                    $tempDetResult = mysqli_query($conn, $tempDetSql);
                    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                    if($tempDetResultCheck > 0) {
                        $itemSql = "SELECT a.cd_item, b.cd_model, b.cd_rom, b.uprc FROM r_item a LEFT JOIN r_unit b ON a.cd_unit=b.cd_unit WHERE a.cd_item='$cd_item' OR a.serial_no='$cd_item' OR a.imei1_no='$cd_item' OR a.imei2_no='$cd_item'";
                        $itemResult = mysqli_query($conn, $itemSql);
                        $itemResultCheck = mysqli_num_rows($itemResult);
                        if($itemResultCheck > 0) {
                            $itemRow = mysqli_fetch_assoc($itemResult);
                            $cd_item = $itemRow['cd_item'];
                            $lccd_model = $itemRow['cd_model'];
                            $lccd_rom = $itemRow['cd_rom'];
                            $lnuprc = $itemRow['uprc'];
                            $lnamount = ($qty * $lnuprc);
                            $tempDetSql = "SELECT a.cd_user, a.cd_stock, a.cd_item, a.qty, a.uprc, a.disc FROM t_tempsrd a LEFT JOIN (SELECT a.cd_item, b.cd_model, b.cd_rom FROM r_item a LEFT JOIN r_unit b ON a.cd_unit=b.cd_unit) b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND b.cd_model='$lccd_model' AND b.cd_rom='$lccd_rom' AND a.no_tran IS NULL ORDER BY a.cd_stock ASC";
                            $tempDetResult = mysqli_query($conn,$tempDetSql);
                            $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                            if($tempDetResultCheck > 0) {
                                $tempDetRow = mysqli_fetch_assoc($tempDetResult);
                                $lccd_item = $tempDetRow['cd_item'];
                                $lccd_stock = $tempDetRow['cd_stock'];
                                $lnqty = abs($tempDetRow['qty']);
                                $lnuprc = $tempDetRow['uprc'];
                                $lnamount = ($lnqty * $lnuprc);
                                $lndisc = abs($tempDetRow['disc']);
                                $lntotal = ($lnamount - $lndisc);
                                if(!empty($lccd_stock)) {
                                    switch(substr($lccd_item,0,2)) {
                                        case 'KL':
                                            if($cd_item != $lccd_item) {
                                                $insertTempDet = "INSERT INTO t_tempsrd(ty_tran,cd_user, cd_item, qty, uprc, amount, disc) VALUES('SI','$lccd_user', '$cd_item', '$lnqty', '$lnuprc', '$lnamount', '$lndisc')";
                                                mysqli_query($conn, $insertTempDet);
                                            } else {
                                                echo "Duplicate Item found.";
                                            }
                                            break;
                                        case 'AC':
                                           $insertTempDet = "INSERT INTO t_tempsrd(ty_tran,cd_user, cd_item, qty, uprc, amount, disc) VALUES('SI','$lccd_user', '$lccd_item', '$lnqty', '$lnuprc', '$lnamount', '$lndisc')";
                                            mysqli_query($conn, $insertTempDet);
                                            break;
                                        default:
                                            echo "Invalid Item Code, Serial, or. IMEI No.";
                                            break;
                                    }
                                } else {
                                    switch(substr($lccd_item,0,2)) {
                                        case 'KL':
                                            echo "Duplicate unit found.";
                                            break;
                                        case 'AC':
                                            $updateTempDet = "UPDATE t_tempsrd SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_stock IS NULL AND cd_item='$lccd_item' AND no_tran IS null";
                                            mysqli_query($conn, $updateTempDet);
                                            break;
                                        default:
                                            echo "Invalid Item Code, Serial, or IMEI No.";
                                            break;
                                    }
                                }
                            } else {
                                $insertTempDet = "INSERT INTO t_tempsrd(ty_tran,cd_user, cd_item, qty, uprc, amount) VALUES('SI','$lccd_user', '$cd_item', '$qty', '$lnuprc', '$lnamount')";
                                mysqli_query($conn,$insertTempDet);
                            }
                        } else {
                            echo 'Invalid Item Code, Serial, or IMEI No.';
                        }
                    } else {
                        echo "Search for old unit first.";
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