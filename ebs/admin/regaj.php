<?php
function docno($prefix,$conn){
  $lcdocno = "";
  $select = "SELECT * FROM r_docno WHERE cd_docno='$prefix'";
  $selectResult = mysqli_query($conn, $select);
  if ($row = mysqli_fetch_assoc($selectResult)){
    if($row['st_series'] <= $row['ed_series']){
      $series = str_pad($row['st_series'],8,"0",STR_PAD_LEFT);
      $update = "UPDATE r_docno SET st_series=st_series+1 WHERE cd_docno='$prefix'";
      $updateResult = mysqli_query($conn, $update);
      $lcdocno = $prefix.$series;
    } else {
      $lcdocno = "NOFILE";
    }
  }
  return $lcdocno;
}
?>
<?php
if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch'])) {
    include_once 'includes/server.php';
    session_start();
    	//opening
    $sgp = $_SESSION['group'];
    if($sgp = 'GS00000001' || $sgp = 'GS00000002' || $sgp = 'GS00000003' || $sgp = '1' || $sgp = '2' || $sgp = '3'){
    $inactivity_time = 526000 * 60;
    }else{
    // Set the inactivity time of 15 minutes (900 seconds)
    $inactivity_time = 30 * 60;
    }

    // Check if the last_timestamp is set
    // and last_timestamp is greater then 15 minutes or 9000 seconds
    // then unset $_SESSION variable & destroy session data
    /*if (isset($_SESSION['last_timestamp']) && (time() - $_SESSION['last_timestamp']) > $inactivity_time) {
        session_unset();
        session_destroy();

        //Redirect user to login page
        header("Location: login.php");
        exit();
    }else{
        // Regenerate new session id and delete old one to prevent session fixation attack
        session_regenerate_id(true);

        // Update the last timestamp
        $_SESSION['last_timestamp'] = time();
    }//closing bracket*/
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_menu = '020';

    $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        $llaction = $row['add'];
        if($llaction=='1') {
            if(isset($_SESSION['id'])) {
                $dt_tran = $_POST['txtdt_tran'];
                $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
                $cd_brnch = (!empty($_POST['cbocd_brnch']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch'])) : '');

                date_default_timezone_set('Asia/Manila');
                $ltt_stamp = date('Y-m-d H:i:s');
                if(empty($dt_tran) || empty($cd_brnch)){
                    echo 'Please input required fields completely.';
                } else {
                    $llcheck = true;
                    $tempDetSql = "SELECT a.*, b.nm_item FROM t_tempajd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.no_tran IS NULL";
                    $tempDetResult = mysqli_query($conn,$tempDetSql);
                    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                    if($tempDetResultCheck > 0) {
                        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                            $lccd_item = $tempDetRow['cd_item'];
                            $lcnm_item = $tempDetRow['nm_item'];
                            $lnqty = $tempDetRow['qty'];
                            if($lnqty >= 0) {
                                continue;
                            }

                            $stockSql = "SELECT a.*, SUM((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot)-a.qty_si) AS qty_soh FROM r_stock a WHERE a.cd_brnch='$cd_brnch' AND a.cd_item='$lccd_item'";
                            $stockResult = mysqli_query($conn,$stockSql);
                            $stockResultCheck = mysqli_num_rows($stockResult);
                            if($stockResultCheck > 0) {
                                $stockRow = mysqli_fetch_assoc($stockResult);
                                $lnqty_soh = $stockRow['qty_soh'];
                                if($lnqty_soh < abs($lnqty)) {
                                    $llcheck = false;
                                    echo "Insufficient inventory. ".$lcnm_item." "."Remaining Qty: ".number_format($lnqty_soh)."<br>";
                                }
                            } else {
                                $llcheck = false;
                                echo "Please set beginning balance. ".$lcnm_item."<br>";
                            }
                        }
                    } else {
                        $llcheck = false;
                        echo "AJ Item details cannot be empty.";
                    }
                    if($llcheck) {
                        $status = 'A';
                        $prefix = 'AJ';
                        $seekDocnoResultCheck = 1;
                        do {
                            $no_doc = docno($prefix,$conn);
                            
                            $seekDocno = "SELECT no_tran FROM t_aj WHERE no_tran='$no_doc'";
                            $seekDocnoResult = mysqli_query($conn, $seekDocno);
                            $seekDocnoResultCheck = mysqli_num_rows($seekDocnoResult);
                        } while($seekDocnoResultCheck > 0);
                        switch ($no_doc) {
                        case "":
                            echo 'File is missing.';
                            break;
                        case "NOFILE":
                            echo 'System reached the records limit. Please contact your Administrator and also send any documents that may support your concerns e.g. Photos or taken screenshots.';
                            break;
                        default:
                            $lntot_cst = 0.00;
                            $tempDetSql = "SELECT * FROM t_tempajd WHERE cd_user='$lccd_user' AND no_tran IS NULL";
                            $tempDetResult = mysqli_query($conn,$tempDetSql);
                            $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                            if($tempDetResultCheck > 0) {
                                while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                                    $lccd_item = $tempDetRow['cd_item'];
                                    $lnqty = $tempDetRow['qty'];
                                    $lnucst = $tempDetRow['ucst'];
                                    $lnamt_cst = ($lnqty * $lnucst);

                                    if($lnqty < 0) {
                                        $lnqty = abs($lnqty);
                                        $lntot_qty = 0.00;
                                        while($lntot_qty < $lnqty) {
                                            $stocklst = "SELECT a.*, (a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot)-a.qty_si AS qty_soh FROM r_stock a WHERE a.cd_brnch='$cd_brnch' AND a.cd_item='$lccd_item' AND (a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot)-a.qty_si > 0 ORDER BY t_add";
                                            $stockResult = mysqli_query($conn,$stocklst);
                                            $stockResultCheck = mysqli_num_rows($stockResult);
                                            if($stockResultCheck > 0) {
                                                $stockRow = mysqli_fetch_assoc($stockResult);
                                                $lnqty_soh = $stockRow['qty_soh'];
                                                $lccd_stock = $stockRow['no_tran'];
                                                $lnucst = $stockRow['ucst'];

                                                if($lnqty_soh >= ($lnqty - $lntot_qty)) {
                                                    $lnqty_ajot = ($lnqty - $lntot_qty);
                                                } else {
                                                    $lnqty_ajot = $lnqty_soh;
                                                }
                                                $lnqty_ajot1 = ($lnqty_ajot * -1);
                                                $lnamt_cst = $lnqty_ajot1 * $lnucst;
                                                $lntot_cst += $lnamt_cst;
                                                $lntot_qty += $lnqty_ajot;

                                                $updateStock = "UPDATE r_stock SET qty_ajot=qty_ajot+$lnqty_ajot1, t_mod='$ltt_stamp' WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                                                mysqli_query($conn,$updateStock);

                                                $insertTranDet = "INSERT INTO t_ajd(no_tran,cd_stock,cd_item,qty,ucst,amt_cst) VALUES('$no_doc','$lccd_stock','$lccd_item','$lnqty_ajot1','$lnucst','$lnamt_cst')";
                                                mysqli_query($conn,$insertTranDet);
                                            }
                                        }
                                    } else {
                                        $lntot_cst = $lntot_cst + $lnamt_cst;
                                        $insertTranDet = "INSERT INTO t_ajd(no_tran,cd_stock,cd_item,qty,ucst,amt_cst) VALUES('$no_doc','$no_doc','$lccd_item','$lnqty','$lnucst','$lnamt_cst')";
                                        mysqli_query($conn,$insertTranDet);

                                        $insertStock = "INSERT INTO r_stock(cd_brnch,no_tran,cd_item,qty_ajin,ucst,cd_user,t_add) VALUES('$cd_brnch','$no_doc','$lccd_item','$lnqty','$lnucst','$lccd_user','$ltt_stamp')";
                                        mysqli_query($conn,$insertStock);
                                    }
                                }
                            }
                            $insert = "INSERT INTO t_aj(no_tran,dt_tran,ref_no,cd_brnch,tot_cst,user_add,t_add,status) VALUES('$no_doc','$dt_tran','$ref_no','$cd_brnch','$lntot_cst','$lccd_user','$ltt_stamp','$status')";
                            mysqli_query($conn,$insert);

                            $sql="DELETE FROM t_tempaj WHERE cd_user='$lccd_user'";
                            mysqli_query($conn, $sql);
                            $sql="DELETE FROM t_tempajd WHERE cd_user='$lccd_user'";
                            mysqli_query($conn, $sql);
                            break;
                        }
                    }
                }
            } else {
                echo 'Session Expired. Please log in.';
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
?>