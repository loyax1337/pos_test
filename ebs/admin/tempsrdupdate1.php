<?php
session_start();
if(isset($_SESSION['id'])) {
    $lccd_user = $_SESSION['id'];
    if(isset($_SESSION['refdet1']) && isset($_SESSION['refdet2']) && isset($_SESSION['refdet3'])) {
        if(isset($_POST['txtqty']) && isset($_POST['txtdisc'])) {
            include_once 'includes/server.php';
        
            $lcref = $_SESSION['ref'];
            $lcrefdet1 = $_SESSION['refdet1'];
            $lcrefdet2 = $_SESSION['refdet2'];
            $lcrefdet3 = $_SESSION['refdet3'];
            $qty = (empty($_POST['txtqty']) ? 0.00 : $_POST['txtqty']);
            $disc = (empty($_POST['txtdisc']) ? 0.00 : $_POST['txtdisc']);

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');

            $tempSql = "SELECT no_si FROM t_tempsr WHERE cd_user='$lccd_user' AND no_tran='$lcref'";
            $tempResult = mysqli_query($conn, $tempSql);
            $tempResultCheck = mysqli_num_rows($tempResult);
            if($tempResultCheck > 0) {
                $tempRow = mysqli_fetch_assoc($tempResult);
                $lcno_si = $tempRow['no_si'];

                switch($lcrefdet3) {
                    case 'SR':
                        $siDetSql = "SELECT qty, uprc FROM t_sid WHERE no_tran='$lcno_si' AND cd_item='$lcrefdet1' AND cd_stock='$lcrefdet2'";
                        $siDetResult = mysqli_query($conn, $siDetSql);
                        $siDetResultCheck = mysqli_num_rows($siDetResult);
                        if($siDetResultCheck > 0) {
                            $siDetRow = mysqli_fetch_assoc($siDetResult);
                            $lnqty_si = $siDetRow['qty'];
                            $lnuprc = $siDetRow['uprc'];
                            $lnamount = ($qty * $lnuprc);

                            if($qty < 0) {
                                if($disc <= 0) {
                                    if(abs($qty) <= $lnqty_si) {
                                        if(abs($disc) <= abs($lnamount)) {
                                            $updateTempDet = "UPDATE t_tempsrd SET qty='$qty', amount='$lnamount', disc='$disc'  WHERE cd_user='$lccd_user' AND no_tran='$lcref' AND cd_item='$lcrefdet1' AND cd_stock='$lcrefdet2' AND ty_tran='$lcrefdet3'";
                                            mysqli_query($conn, $updateTempDet);
                                        } else {
                                            echo "Discount cannot be exceeded.";
                                        }
                                    } else {
                                        echo "Only ".number_format($lnqty_si)." item(s) can be returned.";
                                    }
                                } else {
                                    echo "Old Unit Discount: It should be a negative value or zero.";
                                }
                            } else {
                                echo "Old Unit Quantity: It should be a negative value.";
                            }
                        }
                        break;
                    case 'SI':
                        $tempDetSql = "SELECT cd_stock, uprc FROM t_tempsrd WHERE cd_user='$lccd_user' AND no_tran='$lcref' AND cd_item='$lcrefdet1'".(!empty($lcrefdet2) ? " AND cd_stock='$lcrefdet2'":" AND cd_stock IS NULL");
                        $tempDetResult = mysqli_query($conn, $tempDetSql);
                        $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                        if($tempDetResultCheck > 0) {
                            $tempDetRow = mysqli_fetch_assoc($tempDetResult);
                            $lnuprc = $tempDetRow['uprc'];
                            $lnamount = ($qty * $lnuprc);

                            if($disc < 0 ) {
                                echo "Please input a valid Discount.";
                            } else {
                                if($qty < 0) {
                                    echo "Please input a valid Quantity.";
                                } else {
                                    if($disc > $lnamount) {
                                        echo "Discount cannot be exceeded.";
                                    } else {
                                    
                                        if($qty > 0) {
                                            switch(substr($lcrefdet1,0,2)) {
                                                case 'KL':
                                                    if($qty > 1) {
                                                        echo "Invalid Quantity.";
                                                    } else {
                                                        $updateTempDet = "UPDATE t_tempsrd SET qty='$qty', amount='$lnamount', disc='$disc' WHERE cd_user='$lccd_user' AND no_tran='$lcref' AND cd_item='$lcrefdet1'".(!empty($lcrefdet2) ? " AND cd_stock='$lcrefdet2'":" AND cd_stock IS NULL")." AND ty_tran='$lcrefdet3'";
                                                        mysqli_query($conn, $updateTempDet);
                                                    }
                                                    break;
                                                case 'AC':
                                                    $updateTempDet = "UPDATE t_tempsrd SET qty='$qty', amount='$lnamount', disc='$disc'  WHERE cd_user='$lccd_user' AND no_tran='$lcref' AND cd_item='$lcrefdet1'".(!empty($lcrefdet2) ? " AND cd_stock='$lcrefdet2'":" AND cd_stock IS NULL")." AND ty_tran='$lcrefdet3'";
                                                    mysqli_query($conn,$updateTempDet);
                                                    break;
                                                default:
                                                    echo "Unknown error.";
                                                    break;
                                            } 
                                        } else {
                                            $deleteTempDet = "DELETE FROM t_tempsrd WHERE cd_user='$lccd_user' AND no_tran='$lcref' AND cd_item='$lcrefdet1'".(!empty($lcrefdet2) ? " AND cd_stock='$lcrefdet2'" : " AND cd_stock IS NULL")." AND ty_tran='$lcrefdet3'";
                                            mysqli_query($conn, $deleteTempDet);
                                        }
                                    }
                                }
                            } 
                        } else {
                            echo "Temporary detail data is compromised.";
                        }
                        break;
                    
                    default:
                        echo "Unknown error.";
                        break;
                }
            } else {
                echo "Temporary data is compromised.";
            }
            mysqli_close($conn);
        } else {
            echo "Invalid Url Parameters.";
        }
    } else {
        echo 'Invalid Reference Parameters.';
    }
} else {
    echo "Session Expired. Please login again.";
}

?>