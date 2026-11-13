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
if (isset($_POST['txtnm_last']) && isset($_POST['txtnm_first']) && isset($_POST['txtnm_midd']) && isset($_POST['txtadd_cust']) && isset($_POST['txtno_contact1'])){
    session_start();
    include_once 'includes/server.php';

    $nm_last = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_last']));
    $nm_first = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_first']));
    $nm_midd = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_midd']));
    $add_cust = strtoupper(mysqli_real_escape_string($conn,$_POST['txtadd_cust']));
    $no_contact1 = mysqli_real_escape_string($conn,$_POST['txtno_contact1']);
    
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    if(empty($nm_last) || empty($nm_first)) {
        echo 'Please input required fields completely.';
    } else {
        $status = 'A';
        $prefix = "CU";
        $seekDocnoResultCheck = 1;
        do{
            $no_doc = docno($prefix,$conn);
            
            $seekDocno = "SELECT cd_cust FROM r_cust WHERE cd_cust='$no_doc'";
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
            $sql = "INSERT INTO r_cust(cd_cust,nm_last,nm_first,nm_midd,add_cust,no_contact1,t_add,user_add,status) VALUES('$no_doc','$nm_last','$nm_first','$nm_midd','$add_cust','$no_contact1','$ltt_stamp','$lccd_user','$status')";
            mysqli_query($conn,$sql);
            break;
        }
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>