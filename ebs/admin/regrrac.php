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
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_menu = '016';

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
                    $status = 'A';
                    $prefix = 'RR';
                    $seekDocnoResultCheck = 1;
                    do {
                        $no_doc = docno($prefix,$conn);
                        
                        $seekDocno = "SELECT no_tran FROM t_rr WHERE no_tran='$no_doc'";
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
                        $tempDetSql = "SELECT * FROM t_temprrd WHERE cd_user='$lccd_user' AND ty_tran='AC' AND no_tran IS NULL";
                        $tempDetResult = mysqli_query($conn,$tempDetSql);
                        $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                        if($tempDetResultCheck > 0) {
                            while ($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                                $lccd_item = $tempDetRow['cd_item'];
                                $lnqty = $tempDetRow['qty'];

                                if($lnqty <=0) {
                                    continue;
                                }
                                
                                //$lnucst = 0.00;
                                //$lnamt_cst = ($lnqty * $lnucst);
                                //$lntot_cst = ($lntot_cst + $lnamt_cst);
                                $insertTranDet = "INSERT INTO t_rrd(no_tran,cd_item,qty) VALUES('$no_doc','$lccd_item','$lnqty')";
                                mysqli_query($conn,$insertTranDet);

                                $insertStock = "INSERT INTO r_stock(cd_brnch,no_tran,cd_item,qty_rr,cd_user,t_add) VALUES('$cd_brnch','$no_doc','$lccd_item','$lnqty','$lccd_user','$ltt_stamp')";
                                mysqli_query($conn,$insertStock);
                            }
                        }
                        $insert = "INSERT INTO t_rr(ty_tran, no_tran,dt_tran,ref_no,cd_brnch,tot_cst,user_add,t_add,status) VALUES('AC','$no_doc','$dt_tran','$ref_no','$cd_brnch','$lntot_cst','$lccd_user','$ltt_stamp','$status')";
                        mysqli_query($conn,$insert);

                        $deleteTemp="DELETE FROM t_temprr WHERE cd_user='$lccd_user' ty_tran='AC'";
                        mysqli_query($conn, $deleteTemp);
                        $deleteTempDet="DELETE FROM t_temprrd WHERE cd_user='$lccd_user' ty_tran='AC'";
                        mysqli_query($conn, $deleteTempDet);
                        break;
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