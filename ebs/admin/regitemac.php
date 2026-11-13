<?php
function docno($prefix,$conn){
  $lcdocno = "";
  $select = "SELECT * FROM r_docref WHERE cd_docno='$prefix'";
  $selectResult = mysqli_query($conn, $select);
  if ($row = mysqli_fetch_assoc($selectResult)){
    if($row['st_series'] <= $row['ed_series']){
      $series = str_pad($row['st_series'],9,"0",STR_PAD_LEFT);
      $update = "UPDATE r_docref SET st_series=st_series+1 WHERE cd_docno='$prefix'";
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
if(isset($_POST['cbocd_categ']) && isset($_POST['cbocd_unit']) && isset($_POST['cbocd_supp'])) {
    include_once 'includes/server.php';
    session_start();
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_menu = '013';

    $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        $llaction = $row['add'];
        if($llaction=='1') {
            if(isset($_SESSION['id'])) {
                $cd_categ = (!empty($_POST['cbocd_categ']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_categ'])) : '');
                $cd_unit = (!empty($_POST['cbocd_unit']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_unit'])) : '');
                $cd_supp = (!empty($_POST['cbocd_supp']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_supp'])) : '');

                date_default_timezone_set('Asia/Manila');
                $ltt_stamp = date('Y-m-d H:i:s');
                
                if(empty($cd_categ)) {
                    echo "Please select a Category.";
                } else {
                    if(empty($cd_unit)) {
                        echo "Please select a Unit.";
                    } else {
                        if(empty($cd_supp)) {
                            echo "Please select a Supplier.";
                        } else {
                            $categSql = "SELECT a.cd_categ, a.nm_categ FROM r_categ a WHERE a.cd_categ='$cd_categ'";
                            $categResult = mysqli_query($conn, $categSql);
                            $categResultCheck = mysqli_num_rows($categResult);
                            if($categResultCheck <= 0) {
                                echo "Selected Category could not be found.";
                            } else {    
                                $unitSql = "SELECT a.cd_unit, a.nm_unit, a.desc_unit, a.cd_model, a.cd_rom, a.cd_color FROM r_unit a WHERE a.cd_unit='$cd_unit'";
                                $unitResult = mysqli_query($conn, $unitSql);
                                $unitResultCheck = mysqli_num_rows($unitResult);
                                if($unitResultCheck <= 0) {
                                    echo "Selected Unit could not be found.";
                                } else {
                                    $unitRow = mysqli_fetch_assoc($unitResult);
                                    $lccd_unit = $unitRow['cd_unit'];
                                    $lcnm_unit = $unitRow['nm_unit'];
                                    $lcdesc_unit = $unitRow['desc_unit'];
                                    
                                    $lccd_model = $unitRow['cd_model'];
                                    $lccd_rom = $unitRow['cd_rom'];
                                    $lccd_color = $unitRow['cd_color'];
                                    
                                    $lcnm_item = $lcnm_unit;
                                    $lcdesc_item = $lcdesc_unit;

                                    $status = 'A';
                                    $prefix = 'AC';
                                    $seekDocnoResultCheck = 1;
                                    do {
                                        $no_doc = docno($prefix,$conn);
                                        
                                        $seekDocno = "SELECT cd_item FROM r_item WHERE cd_item='$no_doc'";
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
                                        $insertTran = "INSERT INTO r_item(cd_item,nm_item,desc_item,cd_categ,cd_unit,cd_model,cd_rom,cd_color,cd_supp,t_add,user_add,status) VALUES('$no_doc','$lcnm_item','$lcdesc_item','$cd_categ','$lccd_unit','$lccd_model','$lccd_rom','$lccd_color','$cd_supp','$ltt_stamp','$lccd_user','$status')";
                                        mysqli_query($conn,$insertTran);
                                        break;
                                    }
                                }
                            }
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