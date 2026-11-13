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
if (isset($_POST['txtnm_categ'])){
    session_start();
    include_once 'includes/server.php';
    
    $nm_categ = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_categ']));

    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    if (empty($nm_categ)){
        echo 'Please input required fields completely.';
    } else {
        $status = 'A';
        $prefix = 'CT';
        $seekDocnoResultCheck = 1;
        do {
            $no_doc = docno($prefix,$conn);
            
            $seekDocno = "SELECT cd_categ FROM r_categ WHERE cd_categ='$no_doc'";
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
            $insert = "INSERT INTO r_categ(cd_categ,nm_categ,t_add,user_add,status) VALUES('$no_doc','$nm_categ','$ltt_stamp','$lccd_user','$status')";
            mysqli_query($conn,$insert);
            break;
        }
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>