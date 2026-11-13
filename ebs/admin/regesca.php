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
session_start();

if(isset($_SESSION['id'])) {
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];

    if(isset($_POST['cbomodelrom']) && isset($_POST['cbocd_categ']) && isset($_POST['txtdt_start']) && isset($_POST['txtdt_end']) && isset($_POST['txtuprc'])){
        include_once 'includes/server.php';

        $cd_modelrom = strtoupper(mysqli_real_escape_string($conn, base64_decode($_POST['cbomodelrom'])));
        $cd_categ = strtoupper(mysqli_real_escape_string($conn, base64_decode($_POST['cbocd_categ'])));
        $cd_model = substr($cd_modelrom,0,10);
        $cd_rom = substr($cd_modelrom,10,10);

        $dt_start = $_POST['txtdt_start']; 
        $dt_end = $_POST['txtdt_end'];
        $uprc = (empty($_POST['txtuprc']) ? 0.00 : $_POST['txtuprc']);

        date_default_timezone_set('Asia/Manila');
        $ltt_stamp = date('Y-m-d H:i:s');
        if(empty($cd_model) && empty($cd_rom)){
            echo 'Please select Unit';
        } else {
            if(empty($cd_categ)) {
                echo "Please select Category.";
            } else {
                if(empty($dt_start)) {
                    echo "Please select Start Date.";
                } else {
                    if(empty($dt_end)) {
                        echo "Please select End Date.";
                    } else {
                        if($dt_end < $dt_start) {
                            echo "Invalid set of Date range.";
                        } else {
                            if($uprc <=0) {
                                echo "Please input a valid Price.";
                            } else {
                                $unitSql = "SELECT cd_model, cd_rom FROM r_unit WHERE cd_model='$cd_model' AND cd_rom='$cd_rom'";
                                $unitResult = mysqli_query($conn, $unitSql);
                                $unitResultCheck = mysqli_num_rows($unitResult);
                                if($unitResultCheck > 0) {
                                    $status = 'A';
                                    $prefix = 'PC';
                                    $seekDocnoResultCheck = 1;
                                    do {
                                        $no_doc = docno($prefix,$conn);
                                        
                                        $seekDocno = "SELECT cd_esca FROM r_esca WHERE cd_esca='$no_doc'";
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
                                        $sql = "INSERT INTO r_esca(cd_esca, cd_model, cd_rom, cd_categ, dt_start, dt_end, uprc, t_add, user_add, status) VALUES('$no_doc','$cd_model','$cd_rom','$cd_categ','$dt_start','$dt_end','$uprc','$ltt_stamp','$lccd_user','$status')";
                                        mysqli_query($conn,$sql);
                                        break;
                                    }
                                } else {
                                    echo "Selected Unit could not be found.";
                                }
                            }
                        }
                    }
                }
            }
        }
        mysqli_close($conn);
    } else {
        echo 'Invalid Url Parameters.';
    }
} else {
    echo "Session Expired. Please login again.";
}
?>