<?php
if (isset($_POST['txtnm_last']) && isset($_POST['txtnm_first']) && isset($_POST['txtnm_midd']) && isset($_POST['txtadd_cust']) && isset($_POST['txtno_contact1']) && isset($_POST['txtrfid_cust'])){
    session_start();
    
    include_once 'includes/server.php';

    $lcref = $_SESSION['ref'];
    $nm_last = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_last']));
    $nm_first = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_first']));
    $nm_midd = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_midd']));
    $add_cust = strtoupper(mysqli_real_escape_string($conn,$_POST['txtadd_cust']));
    $no_contact1 = mysqli_real_escape_string($conn,$_POST['txtno_contact1']);
    $rfid_cust = strtoupper(mysqli_real_escape_string($conn,$_POST['txtrfid_cust']));
    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if (empty($nm_last) || empty($nm_first)){
        echo 'Please input required fields completely.';
    } else {
        $sql = "UPDATE r_cust SET nm_last='$nm_last',nm_first='$nm_first',nm_midd='$nm_midd',add_cust='$add_cust',no_contact1='$no_contact1',rfid_cust='$rfid_cust',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE cd_cust='$lcref'";
        mysqli_query($conn,$sql);
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>