<?php
if (isset($_POST['txtnm_last']) || isset($_POST['txtnm_first']) || isset($_POST['txtnm_midd']) || isset($_POST['txtsuffix']) || isset($_POST['cbogender']) || isset($_POST['txtdt_birth']) || isset($_POST['txtemail']) || isset($_POST['txtno_contact1']) || isset($_POST['txtno_contact2']) || isset($_POST['cbogroup']) || isset($_POST['cbonm_brnch'])){
    session_start();
    include_once 'includes/server.php';
    
    //saveSession();
    $lcref = $_SESSION['ref'];
    $nm_last = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_last']));
    $nm_first = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_first']));
    $nm_midd = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_midd']));
    $suffix = strtoupper(mysqli_real_escape_string($conn,$_POST['txtsuffix']));
    
    $gender = strtoupper(mysqli_real_escape_string($conn,$_POST['cbogender']));
    $dt_birth = $_POST['txtdt_birth'];
    $email = mysqli_real_escape_string($conn,$_POST['txtemail']);
    $no_contact1 = mysqli_real_escape_string($conn,$_POST['txtno_contact1']);
    $no_contact2 = mysqli_real_escape_string($conn,$_POST['txtno_contact2']);
    $cd_group = (!empty($_POST['cbogroup']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbogroup'])) : '');
    $cd_brnch = (!empty($_POST['cbonm_brnch']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbonm_brnch'])) : '');

    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if (empty($nm_last) || empty($nm_first) || empty($gender) || empty($cd_group) || empty($cd_brnch)){
        echo 'Please input required fields completely.';
    } else {
        $sql = "UPDATE r_user SET nm_last='$nm_last',nm_first='$nm_first',nm_midd='$nm_midd',suffix='$suffix',gender='$gender',dt_birth='$dt_birth',email='$email',no_contact1='$no_contact1',no_contact2='$no_contact2',cd_group='$cd_group',cd_brnch='$cd_brnch',t_mod='$ltt_stamp' WHERE cd_user='$lcref'";
        mysqli_query($conn,$sql);
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    //clearSession();
    echo 'Invalid Url Parameters.';
}
?>