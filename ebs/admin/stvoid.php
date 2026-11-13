<?php
    session_start();
    if(isset($_SESSION['id'])) {
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];
        $lccd_brnch = $_SESSION['branch'];
        $lcref = $_SESSION['ref'];

        if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch1']) && isset($_POST['cbocd_brnch2'])) {
            include_once 'includes/server.php';
            $lccd_menu = '017';

            $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck > 0) {
                $row = mysqli_fetch_assoc($result);
                $llaction = $row['edit'];
                if($llaction=='1') {
                    $dt_tran = $_POST['txtdt_tran'];
                    $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
                    $cd_brnch1 = (!empty($_POST['cbocd_brnch1']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch1'])) : '');
                    $cd_brnch2 = (!empty($_POST['cbocd_brnch2']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch2'])) : '');
                    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

                    date_default_timezone_set('Asia/Manila');
                    $ltt_stamp = date('Y-m-d H:i:s');
                    if(empty($dt_tran) || empty($cd_brnch1) || empty($cd_brnch2)) {
                        echo 'Please input required fields completely.';
                    } else {
                        $tranSql = "SELECT cd_brnch1, cd_brnch2, status FROM t_st WHERE no_tran='$lcref'";
                        $tranResult = mysqli_query($conn, $tranSql);
                        $tranResultCheck = mysqli_num_rows($tranResult);
                        if($tranResultCheck > 0) {
                            $tranRow = mysqli_fetch_assoc($tranResult);
                            $lccd_brnch1 = $tranRow['cd_brnch1'];
                            $lccd_brnch2 = $tranRow['cd_brnch2'];
                            $lcstatus = $tranRow['status'];

                            if($cd_brnch1 == $cd_brnch2) {
                                echo "Origin and Destination must not be the same.";
                            } else {
                                switch($lcstatus) {
                                case 'V':
                                    echo "This transaction is already voided.";
                                    break;
                                case 'A':
                                    echo "Approved Transaction can no longer be modified.";
                                    break;
                                default:
                                    $updateTran = "UPDATE t_st SET status='V', user_mod='$lccd_user', t_mod='$ltt_stamp' WHERE no_tran='$lcref'";
                                    mysqli_query($conn, $updateTran);

                                    $deleteTemp="DELETE FROM t_tempst WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                                    mysqli_query($conn, $deleteTemp);
                                    $deleteTempDet="DELETE FROM t_tempstd WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                                    mysqli_query($conn, $deleteTempDet);
                                    break;
                                }
                            }
                            $lcref ='';
                        } else {
                            echo "The reference ID could not be found.";
                        }
                    }
                } else {
                    echo 'Access denied! Group access rights cannot perform this action.';
                }
            } else {
                echo 'Access denied! Group access rights cannot perform this action.';
            }
            mysqli_close($conn);
        } else {
            echo 'Invalid Url Parameters.';
        }
    } else {
        echo "Session Expired. Please login again.";
    }
?>