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
if(isset($_POST['txtnm_unit']) && isset($_POST['edtdesc_unit']) && isset($_POST['cbocd_model']) && isset($_POST['cbocd_roms']) && isset($_POST['cbocd_colors']) && isset($_POST['cbocd_brnch']) && isset($_POST['txtuprc']) && isset($_POST['txtdt_eff']) && isset($_POST['fileimg_src'])) {
    include_once 'includes/server.php';
    session_start();
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_menu = '011';

    $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        $llaction = $row['add'];
        if($llaction=='1') {
            if(isset($_SESSION['id'])) {
                $nm_unit = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_unit']));
                $desc_unit = trim(mysqli_real_escape_string($conn,$_POST['edtdesc_unit']));
                $cd_model = (!empty($_POST['cbocd_model']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_model'])) : '');
                $cd_roms = (!empty($_POST['cbocd_roms']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_roms'])) : 'RV00000001');
                $cd_colors = (!empty($_POST['cbocd_colors']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_colors'])) : 'CV00000001');
                $cd_brnch = (!empty($_POST['cbocd_brnch']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch'])) : '');
                $uprc = (empty($_POST['txtuprc']) ? 0.00 : (($_POST['txtuprc'] <= 0.00) ? 0.00 : $_POST['txtuprc']));
                $dt_eff = $_POST['txtdt_eff'];
                $img_src = $_POST['fileimg_src'];


                $img_src = $_POST['fileimg_src'];

                date_default_timezone_set('Asia/Manila');
                $ltt_stamp = date('Y-m-d H:i:s');
                //if(empty($nm_unit)) {
                    //echo "Please input Unit Name.";
                //} else {
                    if(empty($cd_model)) {
                        echo "Please select a Model.";
                    } else {
                        if(empty($cd_roms)) {
                            echo "Please select Capacity Variations.";
                        } else {
                            if(empty($cd_colors)) {
                                echo "Please select Color Variations.";
                            } else {
                                $sql = "SELECT nm_unit FROM r_unit WHERE nm_unit='$nm_unit'";
                                $result = mysqli_query($conn, $sql);
                                $resultCheck = mysqli_num_rows($result);
                                if($resultCheck > 0) {
                                    echo "Duplicate record name found.";
                                } else {
                                    $modelSql = "SELECT a.cd_model, a.nm_model FROM r_model a WHERE a.cd_model='$cd_model'";
                                    $modelResult = mysqli_query($conn, $modelSql);
                                    $modelResultCheck = mysqli_num_rows($modelResult);
                                    if($modelResultCheck <= 0) {
                                        echo "Selected Model could not be found.";
                                    } else {
                                        $modelRow = mysqli_fetch_assoc($modelResult);
                                        $lcnm_model = $modelRow['nm_model'];

                                        $romsdSql = "SELECT a.cd_rom, b.nm_rom FROM r_romsd a LEFT JOIN r_rom b ON a.cd_rom=b.cd_rom WHERE a.cd_roms='$cd_roms'";
                                        $romsdResult = mysqli_query($conn, $romsdSql);
                                        $romsdResultCheck = mysqli_num_rows($romsdResult);
                                        if($romsdResultCheck <= 0) {
                                            echo "Selected Capacity Variations could not found.";
                                        } else {
                                            while($romsdRow = mysqli_fetch_assoc($romsdResult)) {
                                                $lccd_rom = $romsdRow['cd_rom'];
                                                $lcnm_rom = ($romsdRow['cd_rom'] != 'RM00000001' ? ' '.$romsdRow['nm_rom'] : '');
                                                
                                                $colorsdSql = "SELECT a.cd_color, b.nm_color FROM r_colorsd a LEFT JOIN r_color b ON a.cd_color=b.cd_color WHERE a.cd_colors='$cd_colors'";
                                                $colorsdResult = mysqli_query($conn, $colorsdSql);
                                                $colorsdResultCheck = mysqli_num_rows($colorsdResult);
                                                if($colorsdResultCheck <= 0) {
                                                    echo "Selected Color Variations could not found.";
                                                } else {
                                                    while($colorsdRow = mysqli_fetch_assoc($colorsdResult)) {
                                                        $lccd_color = $colorsdRow['cd_color'];
                                                        $lcnm_color = ($colorsdRow['cd_color'] != 'CL00000001' ? ' '.$colorsdRow['nm_color'] : '');
                                                        $lcnm_unit = $lcnm_model.$lcnm_rom.$lcnm_color;

                                                        $status = 'A';
                                                        $prefix = 'UN';
                                                        $seekDocnoResultCheck = 1;
                                                        do {
                                                            $no_doc = docno($prefix,$conn);
                                                            
                                                            $seekDocno = "SELECT cd_unit FROM r_unit WHERE cd_unit='$no_doc'";
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
                                                            $insertTran = "INSERT INTO r_unit(cd_unit,nm_unit,desc_unit,cd_model,cd_roms,cd_rom,cd_colors,cd_color,img_src,t_add,user_add,status) VALUES('$no_doc','$lcnm_unit','$desc_unit','$cd_model','$cd_roms','$lccd_rom','$cd_colors','$lccd_color','$img_src','$ltt_stamp','$lccd_user','$status')";
                                                            mysqli_query($conn,$insertTran);


                                                            if(($uprc > 0) && !empty($dt_eff)) {
                                                                $updateTran = "UPDATE r_unit SET uprc='$uprc', dt_eff='$dt_eff' WHERE cd_unit='$no_doc'";
                                                                mysqli_query($conn, $updateTran);

                                                                if(!empty($cd_brnch)) {
                                                                    $insertPriceHistory = "INSERT INTO r_uprc (cd_brnch, dt_eff, cd_unit, uprc, t_add, user_add) VALUES('$cd_brnch','$dt_eff','$no_doc','$uprc','$ltt_stamp','$lccd_user')";
                                                                    mysqli_query($conn, $insertPriceHistory);
                                                                } else {
                                                                    $brnchSql = "SELECT cd_brnch FROM r_brnch WHERE status='A'";
                                                                    $brnchResult = mysqli_query($conn, $brnchSql);
                                                                    $brnchResultCheck = mysqli_num_rows($brnchResult);
                                                                    if($brnchResultCheck > 0) {
                                                                        while($brnchRow = mysqli_fetch_assoc($brnchResult)) {
                                                                            $lccd_brnch = $brnchRow['cd_brnch'];

                                                                            $insertPriceHistory = "INSERT INTO r_uprc (cd_brnch, dt_eff, cd_unit, uprc, t_add, user_add) VALUES('$lccd_brnch','$dt_eff','$no_doc','$uprc','$ltt_stamp','$lccd_user')";
                                                                            mysqli_query($conn, $insertPriceHistory);
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                //}
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