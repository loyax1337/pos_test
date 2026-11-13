<?php
if (isset($_POST['txtnm_rom'])){
    session_start();
    include_once 'includes/server.php';

    $lcref = $_SESSION['ref'];
    $nm_rom = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_rom']));
    
    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if(empty($nm_rom)){
        echo 'Please input required fields completely.';
    } else {
        $romSql = "SELECT cd_rom FROM r_rom WHERE cd_rom!='$lcref' AND nm_rom='$nm_rom'";
        $romResult = mysqli_query($conn, $romSql);
        $romResultCheck = mysqli_num_rows($romResult);
        if($romResultCheck > 0) {
            echo "Duplicate record name found.";
        } else {
            $sql = "UPDATE r_rom SET nm_rom='$nm_rom',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE cd_rom='$lcref'";
            mysqli_query($conn,$sql);
        }
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>