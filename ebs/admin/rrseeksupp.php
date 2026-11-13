<?php
if (isset($_POST['txtref_no']) && isset($_POST['cbocd_supp'])){   
    session_start();
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    include_once 'includes/server.php';

    $ref_no = mysqli_real_escape_string($conn,$_POST['txtref_no']);
    $cd_supp = (!empty($_POST['cbocd_supp']) ? mysqli_real_escape_string($conn,$_POST['cbocd_supp']) : '');

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $updateTemp = "UPDATE t_temprr SET ref_no='$ref_no' WHERE cd_user='$lccd_user' AND no_tran IS null";
    mysqli_query($conn, $updateTemp);

    $suppSql = "SELECT a.nm_supp, a.add_supp FROM r_supp a WHERE a.cd_supp='$cd_supp'";
    $suppResult = mysqli_query($conn, $suppSql);
    $suppResultCheck = mysqli_num_rows($suppResult);
    if($suppResultCheck > 0) {
        $suppRow = mysqli_fetch_assoc($suppResult);
        $lcnm_supp = $suppRow['nm_supp'];
        $lcadd_supp = $suppRow['add_supp'];

        $updateTemp = "UPDATE t_temprr SET cd_supp='$cd_supp',nm_supp='$lcnm_supp',add_supp='$lcadd_supp' WHERE cd_user='$lccd_user' AND no_tran IS null";
        mysqli_query($conn, $updateTemp);

        //$deleteTempd = "DELETE FROM t_temprrd WHERE cd_user='$lccd_user' AND no_tran IS null";
        //mysqli_query($conn, $deleteTempd);
    } else {
        echo 'Selected supplier not found.';
    }

    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>