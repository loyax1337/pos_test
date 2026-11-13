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

if(isset($_POST['txtnm_model'])){
    session_start();
    include_once 'includes/server.php';

    $nm_model = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_model']));
    
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];

    date_default_timezone_set('Asia/Manila');
    $ltt_add = date('Y-m-d H:i:s');
    if (empty($nm_model)){
        echo 'Please input required fields completely.';
    } else {
        $modelSql ="SELECT cd_model FROM r_model WHERE nm_model='$nm_model'";
        $modelResult = mysqli_query($conn, $modelSql);
        $modelResultCheck = mysqli_num_rows($modelResult);
        if($modelResultCheck > 0 ) {
            echo "Duplicate record name found.";
        } else {
            $status = 'A';
            $prefix = 'MD';
            $seekDocnoResultCheck = 1;
            do {
                $no_doc = docno($prefix,$conn);
                
                $seekDocno = "SELECT cd_model FROM r_model WHERE cd_model='$no_doc'";
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
                $sql = "INSERT INTO r_model(cd_model,nm_model,t_add,user_add,status) VALUES('$no_doc','$nm_model','$ltt_add','$lccd_user','$status')";
                mysqli_query($conn,$sql);
                break;
            }
        }
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>