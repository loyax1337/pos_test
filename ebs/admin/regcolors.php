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

if(isset($_POST['txtnm_colors'])) {
    include_once 'includes/server.php';
    session_start();
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    $lccd_menu = '010';

    $groupDetSql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
    $groupDetResult = mysqli_query($conn, $groupDetSql);
    $groupDetResultCheck = mysqli_num_rows($groupDetResult);
    if($groupDetResultCheck > 0) {
        $groupDetRow = mysqli_fetch_assoc($groupDetResult);
        $llaction = $groupDetRow['add'];
        if($llaction=='1') {
            if(isset($_SESSION['id'])) {
                $nm_colors = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_colors']));

                date_default_timezone_set('Asia/Manila');
                $ltt_stamp = date('Y-m-d H:i:s');
                if(empty($nm_colors)){
                    echo 'Please input required fields completely.';
                } else {
                    $colorsSql = "SELECT cd_colors FROM r_colors WHERE nm_colors='$nm_colors'";
                    $colorsResult = mysqli_query($conn, $colorsSql);
                    $colorsResultCheck = mysqli_num_rows($colorsResult);
                    if($colorsResultCheck > 0) {
                        echo "Duplicate record name found.";
                    } else {
                        $status = 'A';
                        $prefix = 'CV';
                        $seekDocnoResultCheck = 1;
                        do {
                            $no_doc = docno($prefix,$conn);
                            
                            $seekDocno = "SELECT cd_colors FROM r_colors WHERE cd_colors='$no_doc'";
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
                            $tempDetSql = "SELECT * FROM t_tempcolorsd WHERE cd_user='$lccd_user' AND cd_colors IS NULL";
                            $tempDetResult = mysqli_query($conn,$tempDetSql);
                            $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                            if($tempDetResultCheck > 0) {
                                while ($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                                    $lccd_color = $tempDetRow['cd_color'];
                                    $insertTranDet = "INSERT INTO r_colorsd(cd_colors,cd_color) VALUES('$no_doc','$lccd_color')";
                                    mysqli_query($conn,$insertTranDet);
                                }
                            }
                            $insertTran = "INSERT INTO r_colors(cd_colors,nm_colors,user_add,t_add,status) VALUES('$no_doc','$nm_colors','$lccd_user','$ltt_stamp','$status')";
                            mysqli_query($conn,$insertTran);

                            $deleteTemp="DELETE FROM t_tempcolors WHERE cd_user='$lccd_user'";
                            mysqli_query($conn, $deleteTemp);
                            $deleteTempDet="DELETE FROM t_tempcolorsd WHERE cd_user='$lccd_user'";
                            mysqli_query($conn, $deleteTempDet);
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