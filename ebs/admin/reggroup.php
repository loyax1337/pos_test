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
/*function clearSession(){
  $_SESSION['clast'] ="";
  $_SESSION['cfirst'] ="";
  $_SESSION['cgender'] ="";
  $_SESSION['cemail'] ="";
  $_SESSION['ccontact1'] ="";
  $_SESSION['ccontact2'] ="";
}
function saveSession(){
  $_SESSION['clast'] = $_POST['txtnm_last'];
  $_SESSION['cfirst'] = $_POST['txtnm_first'];
  $_SESSION['cgender'] = $_POST['rdogender'];
  $_SESSION['cemail'] = $_POST['txtemail'];
  $_SESSION['ccontact1'] = $_POST['txtno_contact1'];
  $_SESSION['ccontact2'] = $_POST['txtno_contact2'];
}*/
?>
<?php
if (isset($_POST['txtnm_group']) && isset($_POST['txturl'])){
    session_start();
    include_once 'includes/server.php';
    
    //saveSession();
    $nm_group = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_group']));
    $url = mysqli_real_escape_string($conn,$_POST['txturl']);
    
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    if (empty($nm_group)){
        echo 'Please input required fields completely.';
    } else {
        $status = 'A';
        $prefix = "GS";
        $seekDocnoResultCheck = 1;
        do{
            $no_doc = docno($prefix,$conn);
            
            $seekDocno = "SELECT cd_group FROM r_group WHERE cd_group='$no_doc'";
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
            $sql = "INSERT INTO r_group(cd_group,nm_group,url,cd_user,t_add,status) VALUES('$no_doc','$nm_group','$url','$lccd_user','$ltt_stamp','$status')";
            mysqli_query($conn,$sql);

            $menulst = "SELECT * FROM r_menu";
            $menuResult = mysqli_query($conn,$menulst);
            $menuResultCheck = mysqli_num_rows($menuResult);
            if($menuResultCheck > 0) {
                while($menuRow = mysqli_fetch_assoc($menuResult)) {
                    $lccd_menu = $menuRow['cd_menu'];
                    $sql = "INSERT INTO r_groupd(cd_group,cd_menu,t_add,status) VALUES('$no_doc','$lccd_menu','$ltt_stamp','$status')";
                    mysqli_query($conn,$sql);
                }
            }
            //clearSession();
            //header("Location: dblst.php?save=success");
            break;
        }
    }
    mysqli_close($conn);
} else {
    //clearSession();
    echo 'Invalid Url Parameters.';
}
?>